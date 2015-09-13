<?php
/**
 * OCR Element Set
 *
 * Creates a file element set for OCR metadata and text.
 *
 * @copyright Daniel Berthereau, 2013-2015
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 */

/**
 * OCR Element Set plugin.
 * @package Omeka\Plugins\OcrElementSet
 */
class OcrElementSetPlugin extends Omeka_Plugin_AbstractPlugin
{
    const DEFAULT_ELEMENT_SET = 'Item Type Metadata';

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'upgrade',
        'uninstall',
        'uninstall_message',
    );

    /**
     * Installs the plugin.
     */
    public function hookInstall()
    {
        // Load elements to add.
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'elements.php';

        // Checks.
        if (isset($elementSetMetadata) && !empty($elementSetMetadata)) {
            $elementSetName = $elementSetMetadata['name'];

            // Don't install if the element set already exists.
            if ($this->_getElementSet($elementSetName)) {
                throw new Omeka_Plugin_Exception('An element set by the name "' . $elementSetName . '" already exists. You must delete that element set before to install this plugin.');
            }
        }

        // Process.
        if (isset($elementSetMetadata) && !empty($elementSetMetadata)) {
            foreach ($elements as &$element) {
                $element['name'] = $element['label'];
                unset($element['old label']);
            }
            insert_element_set($elementSetMetadata, $elements);
        }
    }

    /**
     * Upgrade the plugin.
     */
    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];

        if (version_compare($oldVersion, '2.3', '<')) {
            $this->_upgradeElements();
        }
    }

    /**
     * Upgrade any changes in elements.
     */
    protected function _upgradeElements()
    {
        // Load elements.
        require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'elements.php';

        $elementSet = get_record('ElementSet', array('name' => $elementSetMetadata['name']));
        $currentElements = $elementSet->getElements();

        // Rename elements.
        foreach ($currentElements as $currentElement) {
            foreach ($elements as $order => $element) {
                $element['name'] = $element['label'];
                if (isset($element['old label']) && $currentElement->name == $element['old label']) {
                    foreach ($element as $elementPropertyName => $elementProperty) {
                        if ($elementPropertyName == 'old label') {
                            continue;
                        }
                        $currentElement->$elementPropertyName = $elementProperty;
                    }
                    // Order starts from one.
                    // $currentElement->order = ++$order;
                    $currentElement->save();
                    break;
                }
            }
        }

        // Add new elements.
        $currentElements = $elementSet->getElements();
        foreach ($elements as $order => $element) {
            $element['name'] = $element['label'];
            $flagElement = false;
            foreach ($currentElements as $currentElement) {
                if ($currentElement->name == $element['name']) {
                    $flagElement = true;
                    break;
                }
            }
            if (!$flagElement) {
                // Order starts from one.
                // $element['order'] = ++$order;
                $elementSet->addElements(array($element));
                $elementSet->save();
            }
        }

        // Move data from old elements to new ones.
        $moveElements = array();
        $currentElements = $elementSet->getElements();
        $elementsToUpdate = array(
            'Total NC' => 0,
            'Total NC dictionnaire' => 0,
            'Taux NC' => 0,
            'Nombre de caractères' => 0,
            'Total caractères douteux' => 0,
            'Taux caractères douteux' => 0,
        );
        $db = $this->_db;
        $elementTable = $db->getTable('Element');
        $newElement = $elementTable
            ->findByElementSetNameAndElementName($elementSet->name, 'Process');
        foreach ($elementsToUpdate as $oldElementName => $elementId) {
            $elementObject = $elementTable
                ->findByElementSetNameAndElementName($elementSet->name, $oldElementName);
            $elementsToUpdate[$oldElementName] = $elementObject->id;
        }

        foreach ($elementsToUpdate as $oldElementName => $elementId) {
            // "Search text" doesn't need to be updated, because the text
            // doesn't change.
            $sql = "
                UPDATE `{$db->ElementText}`
                SET `element_id` = {$newElement->id},
                    `text` = CONCAT('$oldElementName', ' : ', `text`)
                WHERE `element_id` = $elementId
            ";
            $db->query($sql);
        }

        // Remove old elements.
        foreach ($currentElements as $currentElement) {
            $flagElement = false;
            foreach ($elements as $order => $element) {
                $element['name'] = $element['label'];
                if ($currentElement->name == $element['name']) {
                    $flagElement = true;
                    break;
                }
            }
            // Delete removed elements.
            if (!$flagElement) {
                $currentElement->delete();
            }
        }
    }

    /**
     * Uninstalls the plugin.
     */
    public function hookUninstall()
    {
        // Load elements to remove.
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'elements.php';

        if (isset($elementSetMetadata) && !empty($elementSetMetadata)) {
            $elementSetName = $elementSetMetadata['name'];
            $this->_deleteElementSet($elementSetName);
        }
    }

    /**
     * Display the uninstall message.
     */
    public function hookUninstallMessage()
    {
        echo __('%sWarning%s: This will remove all the OCR elements added '
        . 'by this plugin and permanently delete all element texts entered in those '
        . 'fields.%s', '<p><strong>', '</strong>', '</p>');
    }

    /**
     * Helper to get an element set.
     */
    private function _getElementSet($elementSetName)
    {
        return $this->_db
            ->getTable('ElementSet')
            ->findByName($elementSetName);
    }

    /**
     * Helper to remove an element.
     */
    private function _deleteElementSet($elementSetName)
    {
        $elementSet = $this->_getElementSet($elementSetName);

        if ($elementSet) {
            $elements = $elementSet->getElements();
            foreach ($elements as $element) {
                $element->delete();
            }
            $elementSet->delete();
        }
    }
}
