<?php
/**
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package DublinCoreExtended
 */

/**
 * Class that encapsulates the DublinCoreExtended plugin's functionality.
 *
 * @copyright Center for History and New Media, 2010
 * @package DublinCoreExtended
 */
class DublinCoreExtendedPlugin
{
    private $_db;
    private $_elements;
    private $_dcElements = array('Title', 'Subject', 'Description',
                                 'Creator', 'Source', 'Publisher',
                                 'Date', 'Contributor', 'Rights',
                                 'Relation', 'Format', 'Language',
                                 'Type', 'Identifier', 'Coverage');

    
    private $isOmekaTwo = false; //set once whether we're in the new Omeka 2.0 world
    

    public function __construct()
    {
        $this->_db = get_db();
        $this->_setElements();
    }

    public static function install()
    {       
        $dces = new DublinCoreExtendedPlugin;
        $dces->_createTable();
        $dces->_addElements();
        $dces->_insertRelationships();
    }

    public static function uninstall()
    {
        $dces = new DublinCoreExtendedPlugin;
        $dces->_dropTable();
        $dces->_deleteElements();
        $dces->_resetOrder();
    }

    public static function upgrade($oldVersion, $newVersion)
    {
        $db = get_db();
        switch ($oldVersion) {
            case '1.0':
                // Fixes a bug that incorrectly set the record type of the new
                // elements to "Item." Sets them to "All" instead.
                $sql = "
                UPDATE `{$db->prefix}elements` e
                SET e.`record_type_id` = (
                    SELECT rt.`id`
                    FROM `{$db->prefix}record_types` rt
                    WHERE rt.`name` = 'All'
                )
                WHERE e.`element_set_id` = (
                    SELECT es.`id`
                    FROM `{$db->prefix}element_sets` es
                    WHERE es.`name` = 'Dublin Core'
                )";
                $db->query($sql);
            default:
                break;
        }
    }

    public static function adminAppendToPluginUninstallMessage()
    {
        echo '<p><strong>Warning</strong>: This will remove all the Dublin Core
        elements added by this plugin and permanently delete all element texts
        entered in those fields.</p>';
    }

    public static function defineResponseContexts($contexts)
    {
        $contexts['dc-rdf'] = array('suffix' => 'dc-rdf',
                                    'headers' => array('Content-Type' => 'text/xml'));
        return $contexts;
    }

    public static function defineActionContexts($contexts, $controller)
    {
        if ($controller instanceof ItemsController) {
            $contexts['browse'][] = 'dc-rdf';
            $contexts['show'][] = 'dc-rdf';
        }
        return $contexts;
    }

    public function getElements()
    {
        return $this->_elements;
    }

    public static function dcextented_initialize()
    {
      add_translation_source(dirname(__FILE__) . '/languages');
    }

    private function _setElements()
    {
        include 'elements.php';
        $this->_elements = $elements;
    }

    private function _createTable()
    {
        // Create the relationships table. The data in this table isn't used
        // yet, but I'm anticipating they will be necessary in the future.
        $sql = "
        CREATE TABLE IF NOT EXISTS `{$this->_db->prefix}dublin_core_extended_relationships` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `element_id` int(10) unsigned NOT NULL,
            `refines_element_id` int(10) unsigned NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $this->_db->query($sql);
    }

    private function _addElements()
    {

        // Add the new elements to the Dublin Core element set.
        $elementSet = $this->_getElementSet();

        // Temporarily set the order of all Dublin Core elements to NULL.
        $this->_setNullOrder();

        // Iterate through the elements.
        foreach ($this->_elements as $key => $element) {

            // The element already exists.
            if (in_array($element['label'], $this->_dcElements)) {
                $e = $this->_getElement($element['label']);

            // Build a new element.
            } else {
                $e = new Element;
                $e->record_type_id = $this->_getRecordTypeId('All');              
                $e->element_set_id = $elementSet->id;
                $e->name           = $element['label'];
                $e->description    = $element['description'];
            }
            $e->order = $key + 1;
            $e->save();
        }
    }

    private function _insertRelationships()
    {
        foreach ($this->_elements as $element) {
            $elementId = $this->_getElement($element['label'])->id;
            if (isset($element['_refines'])) {
                $refinesElementId = $this->_getElement($element['_refines'])->id;
            } else {
                $refinesElementId = 0;
            }
            $sql = "
            INSERT INTO `{$this->_db->prefix}dublin_core_extended_relationships` (
                `element_id` ,
                `refines_element_id`
            ) VALUES (?, ?)";
            $this->_db->query($sql, array($elementId, $refinesElementId));
        }
    }

    private function _dropTable()
    {
        $sql = "DROP TABLE IF EXISTS `{$this->_db->prefix}dublin_core_extended_relationships`";
        $this->_db->query($sql);
    }

    private function _deleteElements()
    {
        // Delete all the elements and element texts.
        foreach ($this->_elements as $element) {
            if (!in_array($element['label'], $this->_dcElements)) {
                $this->_getElement($element['label'])->delete();
            }
        }
    }

    private function _resetOrder()
    {
        $this->_setNullOrder();

        foreach ($this->_dcElements as $key => $elementName) {
            $e = $this->_getElement($elementName);
            $e->order = $key + 1;
            $e->save();
        }
    }

    private function _getElementSet()
    {
        return $this->_db->getTable('ElementSet')->findByName('Dublin Core');
    }

    private function _getElement($elementName)
    {
        return $this->_db
                    ->getTable('Element')
                    ->findByElementSetNameAndElementName('Dublin Core', $elementName);
    }

    private function _getRecordTypeId($recordTypeName)
    {
        return $this->_db->getTable('RecordType')->findIdFromName($recordTypeName);
    }


    private function _getDataTypeId($dataTypeName)
    {
        return $this->_db->getTable('DataType')->findIdFromName($dataTypeName);
    }

    private function _setNullOrder()
    {
        $sql = "
        UPDATE `{$this->_db->prefix}elements` e
        SET e.`order` = NULL
        WHERE e.`element_set_id` = (
            SELECT es.`id`
            FROM `{$this->_db->prefix}element_sets` es
            WHERE es.`name` = 'Dublin Core'
        )";
        $this->_db->query($sql);
    }
}
