<?php
/**
 * Upgrade OCR data.
 *
 * @todo Divide sql queries in multiple sub-jobs.
 */
class OcrElementSet_UpgradeOcr extends Omeka_Job_AbstractJob
{
    protected $_limit = 100;

    public function perform()
    {
        // OCR data need too be simplified.
        $this->_log(__('Launch conversion process of OCR Data (memory: %memory%).'));
        $this->_upgradeOcrData();
        $this->_log(__('Upgrade process finished (memory: %memory%).'));
    }

    /**
     * Upgrade OCR Data.
     *
     * If process fails, it can (and should) be relaunched: updated data are
     * checked and saved one by one.
     */
    protected function _upgradeOcrData()
    {
        $db = get_db();

        $ocrDataElementId = $db->getTable('Element')
            ->findByElementSetNameAndElementName('OCR', 'Data');
        if (empty($ocrDataElementId)) {
            throw new Omeka_Plugin_Exception('No OCR:Data element! The plugin is not upgraded.');
        }
        $ocrDataElementId = $ocrDataElementId->id;

        // Process is done by item and by file.

        // List of items ids.
        $sql = "
        SELECT items.id
        FROM `$db->Item` AS items
        ORDER BY items.id ASC
        ";
        $items_ids = $db->fetchCol($sql);
        foreach ($items_ids as $item_id) {
            // List of files ids.
            $bind = array($item_id);
            $sql = "
            SELECT files.id
            FROM `$db->File` AS files
            WHERE files.item_id = ?
            ORDER BY files.id ASC
            ";
            $files_ids = $db->fetchCol($sql, $bind);
            foreach ($files_ids as $file_id) {
                $ocrDataElementTexts = $db->getTable('ElementText')->findBy(array(
                    'element_id' => $ocrDataElementId,
                    'record_type' => 'File',
                    'record_id' => $file_id,
                ));
                // Should be single.
                foreach ($ocrDataElementTexts as $ocrData) {
                    if (strlen($ocrData->text) === 0) {
                        $ocrData->delete();
                    }
                    // There is something to process.
                    else {
                        $data = json_decode($ocrData->text, true);
                        // The process is done in two steps.
                        $newData = $this->_simplifyOcrData($data);
                        $newData = $this->_improveOcrData($data);
                        $newData = json_encode($newData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        if ($newData != $ocrData->text) {
                            $ocrData->text = $newData;
                            $ocrData->html = false;
                            $ocrData->save();
                        }
                    }
                    release_object($ocrData);
                }
                $this->_log(__('Conversion of OCR data done for item %s, file %s.', $item_id, $file_id), Zend_Log::DEBUG);
            }
            $this->_log(__('Conversion of OCR data done for item %s.', $item_id));
        }
    }

    /**
     * Simplify OCR Data (formats Simple or Medium to Court).
     *
     * From:
     * {"String":{"0":{"q":"184","c":"Ome","s":"Omeka","h":27,"x":306,"y":307,"w":62}}}'
     * To:
     * {"String":{"0":["Ome",306,307,62,27,184,"Omeka"]}}
     *
     * @param array $data
     * @return array Data
     */
    protected function _simplifyOcrData($data)
    {
        if (empty($data) || !isset($data['String']) || empty($data['String'])) {
            return array('String' => array());
        }

        // Type can be "medium" or "simple".
        if (isset($data['String'][0]['x'])) {
            $type = 'simple';
        }
        elseif (isset($data['String'][0]['hpos'])) {
            $type = 'medium';
        }
        // Already converted.
        // elseif (count($data['String'][0]) >= 5 && count($data['String'][0]) <= 7) {
        //     return $data;
        // }
        // Not managed, unknown or already convert.
        else {
            return $data;
        }

        $newData = array();

        switch ($type) {
            case 'simple':
                foreach ($data['String'] as $position => $values) {
                    $newData[$position] = array();
                    $newData[$position][] = $values['c'];
                    $newData[$position][] = $values['x'];
                    $newData[$position][] = $values['y'];
                    $newData[$position][] = $values['w'];
                    $newData[$position][] = $values['h'];
                    if (isset($values['q'])) {
                        $newData[$position][] = (string) $values['q'];
                    }
                    if (isset($values['s'])) {
                        if (!isset($values['q'])) {
                            $newData[$position][] = '';
                        }
                        $newData[$position][] = $values['s'];
                    }
                }
                break;

            case 'medium':
                foreach ($data['String'] as $position => $values) {
                    $newData[$position] = array();
                    $newData[$position][] = $values['content'];
                    $newData[$position][] = $values['hpos'];
                    $newData[$position][] = $values['vpos'];
                    $newData[$position][] = $values['width'];
                    $newData[$position][] = $values['height'];
                    if (isset($values['cc'])) {
                        $newData[$position][] = (string) $values['cc'];
                    }
                    if (isset($values['subs-content'])) {
                        if (!isset($values['cc'])) {
                            $newData[$position][] = '';
                        }
                        $newData[$position][] = $values['subs-content'];
                    }
                }
                break;
        }

        return array('String' => $newData);
    }

    /**
     * Improve OCR Data.
     *
     * From:
     * {"String":{"0":["Ome",306,307,62,27,"184","Omega"]}...}
     * To:
     {"String":{
        "0":["Omega",[
            ["Ome", 306,307,62,27,"010"],
            ["-",368,307,18,27,"1"],
            ["ga",130,343,45,27,"10"]
        ]]
    }
     *
     * @param array $data
     * @return array Data
     */
    protected function _improveOcrData($data)
    {
        if (empty($data) || !isset($data['String']) || empty($data['String'])) {
            return array('String' => array());
        }

        $newData = array();

        // This copy allows to get next strings.
        $dataString = $data['String'];
        foreach ($data['String'] as $position => $values) {
            // With a hyphen.
            if (isset($values[6])) {
                $string = array();
                $flag = true;

                $content = (string) $values[6];

                while ($values && isset($values[6])) {
                    $value = array(
                        $values[0],
                        $values[1],
                        $values[2],
                        $values[3],
                        $values[4],
                    );
                    if (isset($values[5])) {
                        $value[] = (string) $values[5];
                    }

                    $string[] = $value;

                    $values = next($dataString);
                }

                $newData[$position] = array();
                $newData[$position][] = $content;
                $newData[$position][] = $string;
            }
            // Normal word (don't append the second part of a word).
            elseif (!isset($values[6])) {
                $newData[$position] = $values;
                next($dataString);
            }
        }

        return array('String' => $newData);
    }

    /**
     * Log an upgrade message. Every message will log the import ID.
     *
     * Messages that have %memory%% will include memory usage information.
     *
     * @param string $message The message to log
     * @param int $priority The priority of the message
     */
    protected function _log($message, $priority = Zend_Log::INFO)
    {
        $prefix = "[OcrElementSet][Upgrade]";
        $message = str_replace('%memory%', memory_get_usage(), $message);
        _log("$prefix $message", $priority);
    }
}
