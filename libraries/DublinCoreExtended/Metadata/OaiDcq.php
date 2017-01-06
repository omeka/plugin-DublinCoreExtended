<?php
/**
 * @package DublinCoreExtended
 * @subpackage MetadataFormats
 * @copyright Copyright 2009-2014 John Flatness, Yu-Hsun Lin
 * @copyright Copyright 2014-2015 Daniel Berthereau
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Class implementing DCMI Metadata Terms metadata output (oai_dcq).
 *
 * This format is not standardized, but used by some repositories.
 * The schema comes from the British Library.
 *
 * @see http://www.bl.uk/schemas/
 * @see http://dublincore.org/documents/dc-xml-guidelines/
 * @see http://dublincore.org/schemas/xmls/qdc/dcterms.xsd
 *
 * @see OaiPmhRepository_Metadata_FormatInterface
 * @package DublinCoreExtended
 * @subpackage Metadata Formats
 */
class DublinCoreExtended_Metadata_OaiDcq implements OaiPmhRepository_Metadata_FormatInterface
{
    /** OAI-PMH metadata prefix */
    const METADATA_PREFIX = 'oai_dcq';

    /** XML namespace for output format */
    const METADATA_NAMESPACE = 'http://www.bl.uk/namespaces/oai_dcq/';

    /** XML schema for output format */
    const METADATA_SCHEMA = 'http://www.bl.uk/schemas/qualifieddc/oai_dcq.xsd';

    /** XML namespace for simple Dublin Core */
    const DC_NAMESPACE_URI = 'http://purl.org/dc/elements/1.1/';

    /** XML namepace for DC element refinements*/
    const DC_TERMS_NAMESPACE_URI = 'http://purl.org/dc/terms/';

    /**
     * Appends Dublin Core metadata.
     *
     * Appends a metadata element, a child element with the required format,
     * and further children for each of the Dublin Core fields present in the
     * item.
     */
    public function appendMetadata($item, $metadataElement)
    {
        $document = $metadataElement->ownerDocument;
        $oai_dcq = $document->createElementNS(
            self::METADATA_NAMESPACE, 'oai_dcq:qualifieddc');
        $metadataElement->appendChild($oai_dcq);

        $oai_dcq->setAttribute('xmlns:oai_dcq', self::METADATA_NAMESPACE);
        $oai_dcq->setAttribute('xmlns:dc', self::DC_NAMESPACE_URI);
        $oai_dcq->setAttribute('xmlns:dcterms', self::DC_TERMS_NAMESPACE_URI);
        $oai_dcq->declareSchemaLocation(self::METADATA_NAMESPACE, self::METADATA_SCHEMA);

        // Each of the 15 unqualified Dublin Core elements, in the order
        // specified by the oai_dc XML schema.
        $dcElementNames = array(
            'title', 'creator', 'subject', 'description',
            'publisher', 'contributor', 'date', 'type',
            'format', 'identifier', 'source', 'language',
            'relation', 'coverage', 'rights',
        );

        // Each of metadata terms.
        require dirname(dirname(dirname(dirname(__FILE__))))
            . DIRECTORY_SEPARATOR . 'elements.php';
        $dcTermElements = &$elements;

        // Must create elements using createElement to make DOM allow a
        // top-level xmlns declaration instead of wasteful and non-compliant
        // per-node declarations.
        foreach ($dcTermElements as $element) {
            $elementName = $element['name'];
            $namespace = in_array($elementName, $dcElementNames)
                ? 'dc:'
                : 'dcterms:';

            $dcElements = $item->getElementTexts(
                'Dublin Core', $element['label']);

            // Prepend the item type, if any.
            if ($elementName == 'type' && get_option('oaipmh_repository_expose_item_type')) {
                if ($dcType = $item->getProperty('item_type_name')) {
                    $oai_dcq->appendNewElement('dc:type', $dcType);
                }
            }

            foreach ($dcElements as $elementText) {
                // This check avoids some issues with useless data.
                $value = trim($elementText->text);
                if (strlen($value) > 0) {
                    $oai_dcq->appendNewElement($namespace . $elementName, $value);
                }
            }

            // Append the browse URI to all results.
            if ($elementName == 'identifier') {
                $oai_dcq->appendNewElement('dc:identifier', record_url($item, 'show', true));

                // Also append an identifier for each file.
                if (get_option('oaipmh_repository_expose_files') && metadata($item, 'has files')) {
                    $files = $item->getFiles();
                    foreach ($files as $file) {
                        $oai_dcq->appendNewElement('dc:identifier', $file->getWebPath('original'));
                    }
                }
            }
        }
    }
}
