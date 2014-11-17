<?php
/**
 * @package OaiPmhRepository
 * @subpackage MetadataFormats
 * @author John Flatness, Yu-Hsun Lin, Daniel Berthereau
 * @copyright Copyright 2009 John Flatness, Yu-Hsun Lin
 * @copyright Copyright 2014 Daniel Berthereau
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Class implementing metadata output for the required oai_dc metadata format,
 * with qualified elements from DCMI Metadata Terms that are unrefined.
 * oai_dc is output of the 15 unqualified Dublin Core fields.
 *
 * @package OaiPmhRepository
 * @subpackage Metadata Formats
 */
class OaiPmhRepository_Metadata_OaiDcUnrefined extends OaiPmhRepository_Metadata_Abstract
{
    /** OAI-PMH metadata prefix */
    const METADATA_PREFIX = 'oai_dc';

    /** XML namespace for output format */
    const METADATA_NAMESPACE = 'http://www.openarchives.org/OAI/2.0/oai_dc/';

    /** XML schema for output format */
    const METADATA_SCHEMA = 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd';

    /** XML namespace for unqualified Dublin Core */
    const DC_NAMESPACE_URI = 'http://purl.org/dc/elements/1.1/';

    /**
     * Appends Dublin Core metadata.
     *
     * Appends a metadata element, a child element with the required format,
     * and further children for each of the Dublin Core fields present in the
     * item.
     */
    public function appendMetadata($metadataElement)
    {
        $oai_dc = $this->document->createElementNS(
            self::METADATA_NAMESPACE, 'oai_dc:dc');
        $metadataElement->appendChild($oai_dc);

        // Must manually specify XML schema uri per spec, but DOM won't include
        // a redundant xmlns:xsi attribute, so we just set the attribute
        $oai_dc->setAttribute('xmlns:dc', self::DC_NAMESPACE_URI);
        $oai_dc->setAttribute('xmlns:xsi', parent::XML_SCHEMA_NAMESPACE_URI);
        $oai_dc->setAttribute('xsi:schemaLocation', self::METADATA_NAMESPACE.' '.
            self::METADATA_SCHEMA);

        // Each of the 15 unqualified Dublin Core elements, in the order
        // specified by the oai_dc XML schema.
        $dcElementNames = array(
            'title', 'creator', 'subject', 'description',
            'publisher', 'contributor', 'date', 'type',
            'format', 'identifier', 'source', 'language',
            'relation', 'coverage', 'rights',
        );

        // Each of metadata terms.
        require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'elements.php';
        $dcTermElements = &$elements;

        // Must create elements using createElement to make DOM allow a
        // top-level xmlns declaration instead of wasteful and non-compliant
        // per-node declarations.
        $namespace = 'dc:';
        foreach ($dcTermElements as $element) {
            $elementName = empty($element['_refines'])
                ? $element['name']
                : strtolower($element['_refines']);

            $dcElements = $this->item->getElementTexts(
                'Dublin Core', $element['label']);

            foreach ($dcElements as $elementText) {
                // This check avoids some issues with useless data.
                $value = trim($elementText->text);
                if ($value || $value === '0') {
                    $this->appendNewElement($oai_dc,
                        $namespace . $elementName, $value);
                }
            }

            // Append the browse URI to all results.
            if ($elementName == 'identifier') {
                $this->appendNewElement($oai_dc,
                    'dc:identifier', record_url($this->item, 'show', true));

                // Also append an identifier for each file.
                if (get_option('oaipmh_repository_expose_files') && metadata($this->item, 'has files')) {
                    $files = $this->item->getFiles();
                    foreach ($files as $file) {
                        $this->appendNewElement($oai_dc,
                            'dc:identifier', $file->getWebPath('original'));
                    }
                }
            }
        }
    }

    /**
     * Returns the OAI-PMH metadata prefix for the output format.
     *
     * @return string Metadata prefix
     */
    public function getMetadataPrefix()
    {
        return self::METADATA_PREFIX;
    }

    /**
     * Returns the XML schema for the output format.
     *
     * @return string XML schema URI
     */
    public function getMetadataSchema()
    {
        return self::METADATA_SCHEMA;
    }

    /**
     * Returns the XML namespace for the output format.
     *
     * @return string XML namespace URI
     */
    public function getMetadataNamespace()
    {
        return self::METADATA_NAMESPACE;
    }
}
