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
 * Class implementing DCMI Metadata Terms metadata output (qdc).
 *
 * This format is not standardized, but used by some repositories, as DSpace,
 * and the mediawiki extension ProofreadPage (https://wikisource.org/wiki/Special:ProofreadIndexOaiSchema/qdc).
 * The schema comes from the Science & Technology Facilities Council of the
 * United Kingdom.
 *
 * @see http://epubs.cclrc.ac.uk/xsd/qdc.xsd
 * @see http://dublincore.org/schemas/xmls/qdc/dcterms.xsd
 *
 * @package OaiPmhRepository
 * @subpackage Metadata Formats
 */
class OaiPmhRepository_Metadata_QDc extends OaiPmhRepository_Metadata_Abstract
{
    /** OAI-PMH metadata prefix */
    const METADATA_PREFIX = 'qdc';

    /** XML namespace for output format */
    const METADATA_NAMESPACE = 'http://epubs.cclrc.ac.uk/xmlns/qdc/';

    /** XML schema for output format */
    const METADATA_SCHEMA = 'http://epubs.cclrc.ac.uk/xsd/qdc.xsd';

    /** XML namespace for qualified Dublin Core */
    const QDC_NAMESPACE_URI = 'http://epubs.cclrc.ac.uk/xmlns/qdc/';

    /** XML namespace for unqualified Dublin Core */
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
    public function appendMetadata($metadataElement)
    {
        $qdc = $this->document->createElementNS(
            self::METADATA_NAMESPACE, 'qdc:qualifieddc');
        $metadataElement->appendChild($qdc);

        // Must manually specify XML schema uri per spec, but DOM won't include
        // a redundant xmlns:xsi attribute, so we just set the attribute
        $qdc->setAttribute('xmlns:qdc', self::QDC_NAMESPACE_URI);
        $qdc->setAttribute('xmlns:dc', self::DC_NAMESPACE_URI);
        $qdc->setAttribute('xmlns:dcterms', self::DC_TERMS_NAMESPACE_URI);
        $qdc->setAttribute('xmlns:xsi', parent::XML_SCHEMA_NAMESPACE_URI);
        $qdc->setAttribute('xsi:schemaLocation', self::METADATA_NAMESPACE . ' '
            . self::METADATA_SCHEMA);

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
        foreach ($dcTermElements as $element) {
            $elementName = $element['name'];
            $namespace = in_array($elementName, $dcElementNames)
                ? 'dc:'
                : 'dcterms:';

            $dcElements = $this->item->getElementTexts(
                'Dublin Core', $element['label']);

            foreach ($dcElements as $elementText) {
                // This check avoids some issues with useless data.
                $value = trim($elementText->text);
                if ($value || $value === '0') {
                    $this->appendNewElement($qdc,
                        $namespace . $elementName, $value);
                }
            }

            // Append the browse URI to all results.
            if ($elementName == 'identifier') {
                $this->appendNewElement($qdc,
                    'dc:identifier', record_url($this->item, 'show', true));

                // Also append an identifier for each file.
                if (get_option('oaipmh_repository_expose_files') && metadata($this->item, 'has files')) {
                    $files = $this->item->getFiles();
                    foreach ($files as $file) {
                        $this->appendNewElement($qdc,
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
