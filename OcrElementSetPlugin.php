<?php
/**
 * OCR Element Set
 *
 * Creates a file element set for OCR metadata and text.
 *
 * @copyright Daniel Berthereau, 2013-2014
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 */

/**
 * OCR Element Set plugin.
 * @package Omeka\Plugins\RefnumElementSet
 */
class OcrElementSetPlugin extends Omeka_Plugin_AbstractPlugin
{
    const DEFAULT_ELEMENT_SET = 'Item Type Metadata';

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
    );

    /**
     * Installs the plugin.
     */
    public function hookInstall()
    {
        // Load elements to add.
        require_once 'elements.php';

        // Checks.
        if (isset($elementSetMetadata) && !empty($elementSetMetadata)) {
            $elementSetName = $elementSetMetadata['name'];

            // Don't install if the element set already exists.
            if ($this->_getElementSet($elementSetName)) {
                throw new Exception('An element set by the name "' . $elementSetName . '" already exists. You must delete that element set before to install this plugin.');
            }
        }

        // Process.
        if (isset($elementSetMetadata) && !empty($elementSetMetadata)) {
            insert_element_set($elementSetMetadata, $elements);
        }
    }

    /**
     * Uninstalls the plugin.
     */
    public function hookUninstall()
    {
        // Load elements to remove.
        require_once 'elements.php';

        if (isset($elementSetMetadata) && !empty($elementSetMetadata)) {
            $elementSetName = $elementSetMetadata['name'];
            $this->_deleteElementSet($elementSetName);
        }
    }

    private function _getElementSet($elementSetName)
    {
        return $this->_db
            ->getTable('ElementSet')
            ->findByName($elementSetName);
    }

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
