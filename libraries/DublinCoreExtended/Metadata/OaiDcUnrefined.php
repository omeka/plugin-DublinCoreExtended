<?php
/**
 * @package DublinCoreExtended
 * @subpackage MetadataFormats
 * @copyright Copyright 2009-2014 John Flatness, Yu-Hsun Lin
 * @copyright Copyright 2014 Daniel Berthereau
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Class implementing metadata output for the required oai_dc metadata format,
 * with qualified elements from DCMI Metadata Terms that are unrefined.
 * oai_dc is output of the 15 unqualified Dublin Core fields.
 *
 * @see OaiPmhRepository_Metadata_FormatInterface
 * @package DublinCoreExtended
 * @subpackage Metadata Formats
 */
class DublinCoreExtended_Metadata_OaiDcUnrefined implements OaiPmhRepository_Metadata_FormatInterface
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
    public function appendMetadata($item, $metadataElement)
    {
        $document = $metadataElement->ownerDocument;
        $oai_dc = $document->createElementNS(
            self::METADATA_NAMESPACE, 'oai_dc:dc');
        $metadataElement->appendChild($oai_dc);

        $oai_dc->setAttribute('xmlns:dc', self::DC_NAMESPACE_URI);
        $oai_dc->declareSchemaLocation(self::METADATA_NAMESPACE, self::METADATA_SCHEMA);

        // Each of the 15 unqualified Dublin Core elements, in the order
        // specified by the oai_dc XML schema.
        $dcElementNames = array(
            'title', 'creator', 'subject', 'description',
            'publisher', 'contributor', 'date', 'type',
            'format', 'identifier', 'source', 'language',
            'relation', 'coverage', 'rights',
        );

        // Each of metadata terms.
        require dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'elements.php';
        $dcTermElements = &$elements;

        // Must create elements using createElement to make DOM allow a
        // top-level xmlns declaration instead of wasteful and non-compliant
        // per-node declarations.
        $namespace = 'dc:';
        foreach ($dcTermElements as $element) {
            $elementName = empty($element['_refines'])
                ? $element['name']
                : strtolower($element['_refines']);

            // Remove elements that are not in the fifteen standard DC elements.
            if (!in_array($elementName, $dcElementNames)) {
                continue;
            }

            $dcElements = $item->getElementTexts(
                'Dublin Core', $element['label']);

            // Prepend the item type, if any.
            if ($elementName == 'type' && get_option('oaipmh_repository_expose_item_type')) {
                if ($dcType = $item->getProperty('item_type_name')) {
                    $oai_dc->appendNewElement('dc:type', $dcType);
                }
            }

            foreach ($dcElements as $elementText) {
                // This check avoids some issues with useless data.
                $value = trim($elementText->text);
                if (strlen($value) > 0) {
                    $oai_dc->appendNewElement($namespace . $elementName, $value);
                }
            }

            // Append the browse URI to all results.
            // Use of element['name'] to avoid duplication with refinements.
            if ($element['name'] == 'identifier') {
                $oai_dc->appendNewElement('dc:identifier', record_url($item, 'show', true));

                // Also append an identifier for each file.
                if (get_option('oaipmh_repository_expose_files') && metadata($item, 'has files')) {
                    $files = $item->getFiles();
                    foreach ($files as $file) {
                        $oai_dc->appendNewElement('dc:identifier', $file->getWebPath('original'));
                    }
                }
            }
        }
    }
}
