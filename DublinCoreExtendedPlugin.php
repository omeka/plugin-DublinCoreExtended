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
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'uninstall_message',
        'upgrade',
        'initialize',
        'items_browse_sql',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'response_contexts',
        'action_contexts',
        'items_browse_params',
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'dublin_core_extended_refinements' => '',
    );

    private $_elements;

    private $_dcElements = array(
        'Title', 'Subject', 'Description', 'Creator', 'Source', 'Publisher',
        'Date', 'Contributor', 'Rights', 'Relation', 'Format', 'Language',
        'Type', 'Identifier', 'Coverage',
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
        foreach ($this->_elements as $key => $element) {
            if (!in_array($element['label'], $this->_dcElements)) {
                $sql = "
                INSERT INTO `{$this->_db->Element}` (`element_set_id`, `name`, `description`)
                VALUES (?, ?, ?)";
                $this->_db->query($sql, array($elementSet->id, $element['label'], $element['description']));
            }
        }

        $refinements = get_view()->getDublinCoreRefinements();
        $this->_options['dublin_core_extended_refinements'] = serialize($refinements);

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

        if (version_compare($args['old_version'], '2.1', '<')) {
            $refinements = $this->_getDublinCoreRefinements();
            set_option('dublin_core_extended_refinements', serialize($refinements));
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
     * Deal with search terms (advanced search).
     *
     * Allows to get refinements when a base Dublin Core is used.
     *
     * @internal This hook is used twice, first to prepare select for the
     * current page, second to get the total count.
     *
     * @internal This should be used, because the filter is not enough for
     * requests with a refinable element that should contains something.
     *
     * @see DublinCoreExtendedPlugin::filterItemsBrowseParams()
     *
     * @param Omeka_Db_Select $select
     * @param array $params
     */
    public function hookItemsBrowseSql($args)
    {
        $params = $args['params'];

        if (!isset($params['dublin_core_extended'])) {
            return;
        }

        $select = $args['select'];
        $db = get_db();

        // Adapted from application/models/Table/Item.php.
        $advancedIndex = 0;
        foreach ($params['dublin_core_extended'] as $filter) {
            $type = $filter['advanced']['type'];
            $value = $filter['advanced']['terms'];
            $refinements = array_keys($filter['refinements']);

            // Determine what the WHERE clause should look like.
            $predicates = array();
            switch ($type) {
                case 'contains':
                    $oneWhere = true;
                    $predicate = 'LIKE ' . $db->quote('%' . $value .'%');
                    break;
                case 'is exactly':
                    $oneWhere = true;
                    $predicate = '= ' . $db->quote($value);
                    break;
                case 'does not contain':
                    $oneWhere = false;
                    $predicates[] = 'NOT LIKE ' . $db->quote('%' . $value .'%');
                    $predicates[] = 'IS NULL';
                    break;
                case 'is empty':
                    $oneWhere = false;
                    $predicates = array('IS NULL');
                    break;
                case 'is not empty':
                    $oneWhere = true;
                    $predicate = 'IS NOT NULL';
                    break;
                default:
                    throw new Omeka_Record_Exception(__('Invalid search type given!'));
            }

            // One where with zero or multiple OR.
            if ($oneWhere) {
                foreach ($refinements as $elementId) {
                    $alias = '_dcAdvanced_' .  $advancedIndex++;
                    $joinCondition = "{$alias}.record_id = items.id AND {$alias}.record_type = 'Item' AND {$alias}.element_id = $elementId";
                    $select->joinLeft(array($alias => $db->ElementText), $joinCondition, array());
                    $predicates[] = $alias . '.text ' . $predicate;
                }
                $where = implode(' OR ', $predicates);
                $select->where($where);
            }

            // Multiple where with zero or one OR.
            else {
                foreach ($refinements as $elementId) {
                    $alias = '_dcAdvanced_' .  $advancedIndex++;
                    $joinCondition = "{$alias}.record_id = items.id AND {$alias}.record_type = 'Item' AND {$alias}.element_id = $elementId";
                    $select->joinLeft(array($alias => $db->ElementText), $joinCondition, array());
                    $select->where($alias . '.text ' . implode(" OR {$alias}.text ", $predicates));
                }
            }
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

    /**
     * Filter items browse params in order to find refinements when a base
     * element is searched.
     *
     * @see DublinCoreExtendedPlugin::hookItemsBrowseSql()
     *
     * @todo Manage the case where multiple hooks need to access Dublin Core
     * params (this should be managed via core).
     *
     * @param array $params
     * @return array
     */
    public function filterItemsBrowseParams($params)
    {
        if (isset($params['advanced'])
                && !empty($params['advanced'])
            ) {
            $advanced = $params['advanced'];
            $result = &$params['advanced'];

            $refinements = unserialize(get_option('dublin_core_extended_refinements'));

            // Manage each advanced selection.
            foreach ($advanced as $key => $param) {
                // Do not search on blank rows.
                if (empty($param['element_id']) || empty($param['type'])) {
                    unset($result[$key]);
                    continue;
                }

                // Process only if the element id is a Dublin Core one.
                if (isset($refinements[$param['element_id']])) {
                    $parent = &$refinements[$param['element_id']];
                    // Check if there are refinements (if not, the element is
                    // already processed via Omeka Core).
                    if (count($parent) > 1) {
                        // Save key for hook.
                        $params['dublin_core_extended'][$key]['advanced'] = $param;
                        $params['dublin_core_extended'][$key]['refinements'] = $parent;
                        // Remove the element from params to avoid two filters.
                        unset($result[$key]);
                    }
                }
            }
        }
        return $params;
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

    /**
     * Get an array of element ids and names of Dublin Core refinements.
     *
     * No distinction is done between Dublin Core base and extended.
     *
     * @return array
     */
    private function _getDublinCoreRefinements()
    {
        // Get the elements list.
        include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'elements.php';

        $refinements = array();
        foreach ($elements as $element) {
            $elementObject = $this->_getDublinCoreElement($element['label']);
            $refinements[$elementObject->id][$elementObject->id] = $elementObject->name;
            if (isset($element['_refines'])) {
                $elementToRefine = $this->_getDublinCoreElement($element['_refines']);
                $refinements[$elementToRefine->id][$elementObject->id] = $elementObject->name;
            }
        }

        return $refinements;
    }

    /**
     * Helper to get a Dublin Core element from a name.
     *
     * @param string $name
     * @return element object
     */
     private function _getDublinCoreElement($name)
     {
         static $elementSet;

        // Get the Dublin Core element set.
         if (empty($elementSet)) {
            $elementSet = get_record('ElementSet', array('name' => 'Dublin Core'));
         }

        $element = get_record('Element', array(
            'element_set_id' => $elementSet->id,
            'name' => $name,
        ));
        return $element;
     }
}
