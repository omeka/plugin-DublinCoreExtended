<?php
/**
 * Dublin Core Extended
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Dublin Core Extended plugin.
 * 
 * @package Omeka\Plugins\DublinCoreExtended
 */
class DublinCoreExtendedPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'uninstall_message',
        'upgrade',
        'initialize',
        'config_form',
        'config',
    );
    
    protected $_filters = array(
        'response_contexts',
        'action_contexts',
        'oai_pmh_repository_metadata_formats',
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'dublin_core_extended_oaipmh_unrefined_dc' => false,
        'dublin_core_extended_oaipmh_oai_dcq' => true,
        'dublin_core_extended_oaipmh_qdc' => false,
    );

    private $_elements;
    
    private $_dcElements = array(
        'Title', 'Creator', 'Subject', 'Description', 'Publisher',
        'Contributor', 'Date', 'Type', 'Format', 'Identifier', 'Source',
        'Language', 'Relation', 'Coverage', 'Rights',
    );

    
    public function __construct()
    {
        parent::__construct();
        // Set the elements.
        include 'elements.php';
        $this->_elements = $elements;
    }
    
    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Add the new elements to the Dublin Core element set. 
        $elementSet = $this->_db->getTable('ElementSet')->findByName('Dublin Core');

        $existingDcElements = array();
        $elementTable = $this->_db->getTable('Element');
        foreach ($elementTable->findBySet('Dublin Core') as $element) {
            $existingDcElements[] = $element->name;
        }

        foreach ($this->_elements as $key => $element) {
            if (!in_array($element['label'], $existingDcElements)) {
                $sql = "
                INSERT INTO `{$this->_db->Element}` (`element_set_id`, `name`, `description`) 
                VALUES (?, ?, ?)";
                $this->_db->query($sql, array($elementSet->id, $element['label'], $element['description']));
            }
        }

        $this->_installOptions();
    }
    
    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        // Delete all the elements and element texts.
        $elementTable = $this->_db->getTable('Element');
        foreach ($this->_elements as $element) {
            if (!in_array($element['label'], $this->_dcElements)) {
                $elementTable->findByElementSetNameAndElementName('Dublin Core', $element['label'])->delete();
            }
        }

        $this->_uninstallOptions();
    }
    
    /**
     * Display the uninstall message.
     */
    public function hookUninstallMessage()
    {
        echo __('%sWarning%s: This will remove all the Dublin Core elements added ' 
        . 'by this plugin and permanently delete all element texts entered in those ' 
        . 'fields.%s', '<p><strong>', '</strong>', '</p>');
    }
    
    /**
     * Upgrade this plugin.
     * 
     * @param array $args
     */
    public function hookUpgrade($args)
    {
        // Drop the unused dublin_core_extended_relationships table.
        if (version_compare($args['old_version'], '2.0', '<')) {
            $sql = "DROP TABLE IF EXISTS `{$this->_db->DublinCoreExtendedRelationship}`";
            $this->_db->query($sql);
        }
    }
    
    /**
     * Initialize this plugin.
     */
    public function hookInitialize()
    {
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    }

    /**
     * Shows plugin configuration page.
     *
     * @return void
     */
    public function hookConfigForm($args)
    {
        $view = $args['view'];
        echo $view->partial(
            'plugins/dublin-core-extended-config-form.php',
            array(
                'view' => $view,
            )
        );
    }

    /**
     * Processes the configuration form.
     *
     * @return void
     */
    public function hookConfig($args)
    {
        $post = $args['post'];
        foreach ($post as $key => $value) {
            set_option($key, $value);
        }
    }

    /**
     * Add the dc-rdf response context.
     * 
     * @param array $contexts
     * @return array
     */
    public function filterResponseContexts($contexts)
    {
        $contexts['dc-rdf'] = array('suffix' => 'dc-rdf', 
                                    'headers' => array('Content-Type' => 'text/xml'));
        return $contexts;
    }
    
    /**
     * Add the dc-rdf response context to items/browse and items/show.
     * 
     * @param array $contexts
     * @param array $args
     * @return array
     */
    public function filterActionContexts($contexts, $args)
    {
        if ($args['controller'] instanceof ItemsController) {
            $contexts['browse'][] = 'dc-rdf';
            $contexts['show'][] = 'dc-rdf';
        }
        return $contexts;
    }

    public function filterOaiPmhRepositoryMetadataFormats($formats)
    {
        if (get_option('dublin_core_extended_oaipmh_unrefined_dc')) {
            $formats['oai_dc'] = array(
                'class' => 'DublinCoreExtended_Metadata_OaiDcUnrefined',
                'namespace' => DublinCoreExtended_Metadata_OaiDcUnrefined::METADATA_NAMESPACE,
                'schema' => DublinCoreExtended_Metadata_OaiDcUnrefined::METADATA_SCHEMA,
            );
        }

        if (get_option('dublin_core_extended_oaipmh_oai_dcq')) {
            $formats['oai_dcq'] = array(
                'class' => 'DublinCoreExtended_Metadata_OaiDcq',
                'namespace' => DublinCoreExtended_Metadata_OaiDcq::METADATA_NAMESPACE,
                'schema' => DublinCoreExtended_Metadata_OaiDcq::METADATA_SCHEMA,
            );
        }

        if (get_option('dublin_core_extended_oaipmh_qdc')) {
            $formats['qdc'] = array(
                'class' => 'DublinCoreExtended_Metadata_QDc',
                'namespace' => DublinCoreExtended_Metadata_QDc::METADATA_NAMESPACE,
                'schema' => DublinCoreExtended_Metadata_QDc::METADATA_SCHEMA,
            );
        }

        return $formats;
    }

    /**
     * Get the dublin core extended elements array.
     * 
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }
}
