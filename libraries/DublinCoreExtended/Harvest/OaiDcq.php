<?php
/**
 * @package DublinCoreExtended
 * @subpackage Models
 * @copyright Copyright (c) 2009-2011 Roy Rosenzweig Center for History and New Media
 * @copyright Copyright 2015 Daniel Berthereau
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Metadata format map for the oai_dcq Qualified Dublin Core format.
 *
 * @see http://www.bl.uk/schemas/
 * @see http://dublincore.org/documents/dc-xml-guidelines/
 * @see http://dublincore.org/schemas/xmls/qdc/dcterms.xsd
 *
 * @package DublinCoreExtended
 */
class DublinCoreExtended_Harvest_OaiDcq extends OaipmhHarvester_Harvest_Abstract
{
    /*  XML schema and OAI prefix for the format represented by this class.
        These constants are required for all maps. */
    const METADATA_SCHEMA = 'http://www.bl.uk/schemas/qualifieddc/oai_dcq.xsd';
    const METADATA_PREFIX = 'oai_dcq';

    const OAI_DCQ_NAMESPACE = 'http://www.bl.uk/namespaces/oai_dcq/';
    const DUBLIN_CORE_NAMESPACE = 'http://purl.org/dc/elements/1.1/';
    const DC_TERMS_NAMESPACE = 'http://purl.org/dc/terms/';

    /**
     * Collection to insert items into.
     * @var Collection
     */
    protected $_collection;

    /**
     * Actions to be carried out before the harvest of any items begins.
     */
     protected function _beforeHarvest()
    {
        $harvest = $this->_getHarvest();

        $collectionMetadata = array(
            'metadata' => array(
                'public' => $this->getOption('public'),
                'featured' => $this->getOption('featured'),
        ));
        $collectionMetadata['elementTexts']['Dublin Core']['Title'][] =
            array('text' => (string) $harvest->set_name, 'html' => false);
        $collectionMetadata['elementTexts']['Dublin Core']['Description'][] =
            array('text' => (string) $harvest->set_Description, 'html' => false);

        $this->_collection = $this->_insertCollection($collectionMetadata);
    }

    /**
     * Harvest one record.
     *
     * @param SimpleXMLIterator $record XML metadata record
     * @return array Array of item-level, element texts and file metadata.
     */
    protected function _harvestRecord($record)
    {
        $itemMetadata = array(
            'collection_id' => $this->_collection->id,
            'public' => $this->getOption('public'),
            'featured' => $this->getOption('featured'),
        );

        $elementTexts = array();

        // Simple DC and Qualified DC are retrieved separetely because
        // namespaces are different.
        $dcMetadata = $record
            ->metadata
            ->children(self::OAI_DCQ_NAMESPACE)
            ->children(self::DUBLIN_CORE_NAMESPACE);
        $dcqMetadata = $record
            ->metadata
            ->children(self::OAI_DCQ_NAMESPACE)
            ->children(self::DC_TERMS_NAMESPACE);

        // Simple dc terms.
        $dcElementsName = array(
            'title', 'creator', 'subject', 'description', 'publisher',
            'contributor', 'date', 'type', 'format', 'identifier',
            'source', 'language', 'relation', 'coverage', 'rights',
        );
        // Each of qualified dc terms with name and label.
        require dirname(dirname(dirname(dirname(__FILE__))))
            . DIRECTORY_SEPARATOR . 'elements.php';

        foreach ($elements as $element) {
            $elementData = array();
            $elementName = $element['name'];
            if (in_array($elementName, $dcElementsName)) {
                if (isset($dcMetadata->$elementName)) {
                    $elementData = $dcMetadata->$elementName;
                }
            }
            else {
                if (isset($dcqMetadata->$elementName)) {
                    $elementData = $dcqMetadata->$elementName;
                }
            }
            foreach ($elementData as $rawText) {
                $text = trim($rawText);
                $elementTexts['Dublin Core'][$element['label']][] =
                    array('text' => (string) $text, 'html' => false);
            }
        }

        return array(
            'itemMetadata' => $itemMetadata,
            'elementTexts' => $elementTexts,
            'fileMetadata' => array(),
        );
    }
}
