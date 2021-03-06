<?php
/**
 * Ingest Alto metadata files into Omeka elements for a file.
 *
 * @package OaiPmhStaticRepository
 */
class OaiPmhStaticRepository_Ingester_Alto extends OaiPmhStaticRepository_Ingester_Abstract
{
    const XML_ROOT = 'alto';
    const XML_PREFIX = 'alto';
    // This namespace may need to be changed.
    const XML_NAMESPACE = 'http://bibnum.bnf.fr/ns/alto_prod';

    protected $_checkMetadataFile = array('extension', 'validate xml');
    protected $_extension = 'xml';
    protected $_formatXml = self::XML_PREFIX;
    protected $_xmlRoot = self::XML_ROOT;
    protected $_xmlNamespace = self::XML_NAMESPACE;
    protected $_xmlPrefix = self::XML_PREFIX;

    // Current doc for internal purposes.
    protected $_doc;

    protected $_xslOcrText = 'libraries/xsl/alto2text.xsl';
    protected $_xslOcrData = 'libraries/xsl/alto2json.xsl';
    protected $_xslOcrProcess = 'libraries/xsl/alto2process.xsl';

    public function __construct($uri, $parameters)
    {
        $base = PLUGIN_DIR
            . DIRECTORY_SEPARATOR . 'OcrElementSet';
        $this->_xslOcrText = $base
            . DIRECTORY_SEPARATOR . $this->_xslOcrText;
        $this->_xslOcrData = $base
            . DIRECTORY_SEPARATOR . $this->_xslOcrData;
        $this->_xslOcrProcess = $base
            . DIRECTORY_SEPARATOR . $this->_xslOcrProcess;

        parent::__construct($uri, $parameters);
    }

    /**
     * Extract metadata from a file to associate them to another file.
     *
     * @param string $filepath The path to the metadata file.
     * @return array Metadata.
     */
    public function extractMetadata($filepath)
    {
        // TODO Check if sub-path.
        $file = $this->_managePaths->getAbsolutePath($filepath);
        if (!$this->_validateFile->isMetadataFile(
                $file,
                $this->_checkMetadataFile,
                array(
                    'extension' => $this->_extension,
                    'xmlRoot' => $this->_xmlRoot,
                    'xmlNamespace' => $this->_xmlNamespace,
                    'xmlPrefix' => $this->_xmlPrefix,
            ))) {
            return array();
        }

        return $this->_extractOcr($file);
    }

    /**
     * Extract ocr from an alto file.
     *
     * @param string $filepath
     * @return array Array of standard metadata, else empty array.
     */
    protected function _extractOcr($filepath)
    {
        $metadata = array();

        // Extract the text via the stylesheet.
        if ($this->_getParameter('ocr_fill_text')) {
            // Process the xml file via the stylesheet.
            $textPath = $this->_processXslt->processXslt($filepath, $this->_xslOcrText);
            if (filesize($textPath) > 0) {
                $metadata['OCR']['Text'][] = file_get_contents($textPath);
            }
        }

        // Extract the data via the stylesheet.
        if ($this->_getParameter('ocr_fill_data')) {
            // Process the xml file via the stylesheet.
            $dataPath = $this->_processXslt->processXslt($filepath, $this->_xslOcrData);
            if (filesize($dataPath) > 0) {
                $metadata['OCR']['Data'][] = file_get_contents($dataPath);
            }
        }

        // Extract the process via the stylesheet.
        if ($this->_getParameter('ocr_fill_process')) {
            // Process the xml file via the stylesheet.
            $processPath = $this->_processXslt->processXslt($filepath, $this->_xslOcrProcess);
            if (filesize($processPath) > 0) {
                $metadata['OCR']['Process'][] = file_get_contents($processPath);
            }
        }

        return $metadata;
    }
}
