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
        'config_form',
        'config',
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
        'dublin_core_extended_refines' => false,
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

        $refinements = $this->_getDublinCoreRefinements();
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
        if (version_compare($args['old_version'], '2.1', '<=')) {
            $refinements = $this->_getDublinCoreRefinements();
            set_option('dublin_core_extended_refinements', serialize($refinements));
            set_option('dublin_core_extended_refines', $this->_options['dublin_core_extended_refines']);
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
    public function hookConfigForm()
    {
        echo get_view()->partial(
            'plugins/dublin-core-extended-config-form.php'
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
     * @internal MySql 5.5 has a limit: group by is done before order by, and it
     * is complex to skirt.
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
        $db = $this->_db;

        if (isset($params['dublin_core_extended']['filters'])) {
            // Adapted from application/models/Table/Item.php.
            $advancedIndex = 0;
            foreach ($params['dublin_core_extended']['filters'] as $filter) {
                $type = $filter['type'];
                $value = $filter['terms'];
                $refinements = array_keys($filter['refinements']);
                $elementIds = implode(',', $refinements);

                $alias = '_dcAdvanced_' .  $advancedIndex++;

                // Determine what the WHERE clause should look like.
                switch ($type) {
                    case 'contains':
                        $predicate = 'LIKE ' . $db->quote('%' . $value .'%');
                        break;
                    case 'is exactly':
                        $predicate = '= ' . $db->quote($value);
                        break;
                    case 'is not empty':
                        $predicate = 'IS NOT NULL';
                        break;
                    case 'does not contain':
                        $predicate = 'NOT LIKE ' . $db->quote('%' . $value .'%') . " OR {$alias}.text IS NULL";
                        break;
                    case 'is empty':
                        $predicate = 'IS NULL';
                        break;
                    default:
                        throw new Omeka_Record_Exception(__('Invalid search type given!'));
                }

                $joinCondition = "{$alias}.record_id = items.id AND {$alias}.record_type = 'Item' AND {$alias}.element_id IN ($elementIds)";
                $select->joinLeft(array($alias => $db->ElementText), $joinCondition, array());
                $select->where($alias . '.text ' . $predicate);
            }
        }

        if (isset($params['dublin_core_extended']['sort_field'])) {
            $sortField = array_keys($params['dublin_core_extended']['sort_field']);
            $sortField = implode(',', $sortField);

            $sortDir = isset($params['sort_dir']) && $params['sort_dir'] == 'd'
                ? 'DESC'
                : 'ASC';

            $select
                ->joinLeft(array('et_sort' => $db->ElementText),
                    "et_sort.record_id = items.id AND et_sort.record_type = 'Item' AND et_sort.element_id IN ($sortField)",
                    array())
                ->reset('order')
                ->group('items.id')
                ->order(array("IF(ISNULL(et_sort.text), 1, 0) $sortDir",
                    "et_sort.text $sortDir"));
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
        if (get_option('dublin_core_extended_refines')) {
            $refinements = unserialize(get_option('dublin_core_extended_refinements'));

            if (isset($params['advanced']) && !empty($params['advanced'])) {
                $advanced = $params['advanced'];
                $result = &$params['advanced'];

                // Manage each advanced selection.
                foreach ($advanced as $key => $param) {
                    // Do not search on blank rows.
                    if (empty($param['element_id']) || empty($param['type'])) {
                        unset($result[$key]);
                        continue;
                    }

                    // Process only if the element id is a Dublin Core one.
                    if (isset($refinements[$param['element_id']])) {
                        $parent = $refinements[$param['element_id']];
                        // Check if there are refinements (if not, the element is
                        // already processed via Omeka Core).
                        if (count($parent) > 1) {
                            // Save key for hook.
                            $params['dublin_core_extended']['filters'][$key] = $param;
                            $params['dublin_core_extended']['filters'][$key]['refinements'] = $parent;
                            // Remove the element from params to avoid two filters.
                            unset($result[$key]);
                        }
                    }
                }
            }

            if (isset($params['sort_field']) && !empty($params['sort_field'])) {
                // Check if the sort field is a Dublin Core one.
                if (strpos($params['sort_field'], 'Dublin Core,') === 0) {
                    // Get the id of the element via the refinements array.
                    $elementName = substr($params['sort_field'], 12);
                    foreach ($refinements as $parent => $refinementList) {
                        $elementId = array_search($elementName, $refinementList);
                        if ($elementId) {
                            $parent = $refinements[$elementId];
                            // Check if there are refinements (if not, the element is
                            // already processed via Omeka Core).
                            if (count($parent) > 1) {
                                $params['dublin_core_extended']['sort_field'] = $parent;
                                // Remove the element from params to avoid two sorts.
                                unset($params['sort_field']);
                            }
                            break;
                        }
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
     * Get an array of element ids and names of Dublin Core refinements to avoid
     * to determine it each time.
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
        $elementTable = $this->_db->getTable('Element');
        foreach ($elements as $element) {
            $elementObject = $elementTable->findByElementSetNameAndElementName('Dublin Core', $element['label']);
            $refinements[$elementObject->id][$elementObject->id] = $elementObject->name;
            if (isset($element['_refines'])) {
                $elementToRefine = $elementTable->findByElementSetNameAndElementName('Dublin Core', $element['_refines']);
                $refinements[$elementToRefine->id][$elementObject->id] = $elementObject->name;
            }
        }

        return $refinements;
    }
}
