<?php
define('DUBLIN_CORE_EXTENDED_PLUGIN_VERSION', '1.0');

add_plugin_hook('install', 'dublin_core_extended_install');
add_plugin_hook('uninstall', 'dublin_core_extended_uninstall');

function dublin_core_extended_install()
{
    $dces = new DublinCoreExtendedPlugin;
    $dces->install();
}

function dublin_core_extended_uninstall()
{
    $dces = new DublinCoreExtendedPlugin;
    $dces->uninstall();
}

class DublinCoreExtendedPlugin
{
    private $_db;
    private $_elements;
    private $_dcElements = array('Title', 'Subject', 'Description', 
                                 'Creator', 'Source', 'Publisher', 
                                 'Date', 'Contributor', 'Rights', 
                                 'Relation', 'Format', 'Language', 
                                 'Type', 'Identifier', 'Coverage');
    
    public function __construct()
    {
        $this->_db = get_db();
        $this->_setElements();
    }
    
    public function install()
    {
        set_option('dublin_core_extended_plugin_version', DUBLIN_CORE_EXTENDED_PLUGIN_VERSION);
        $this->_createTable();
        $this->_addElements();
        $this->_insertRelationships();
    }
    
    public function uninstall()
    {
        delete_option('dublin_core_extended_plugin_version');
        $this->_dropTable();
        $this->_deleteElements();
        $this->_resetOrder();
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
            if (in_array($element['name'], $this->_dcElements)) {
                $e = $this->_getElement($element['name']);
            
            // Build a new element.
            } else {
                $e = new Element;
                $e->record_type_id = $this->_getRecordTypeId('Item');
                $e->data_type_id   = $this->_getDataTypeId($element['data_type']);
                $e->element_set_id = $elementSet->id;
                $e->name           = $element['name'];
                $e->description    = $element['description'];
            }
            $e->order = $key + 1;
            $e->save();
        }
    }
    
    private function _insertRelationships()
    {
        foreach ($this->_elements as $element) {
            $elementId = $this->_getElement($element['name'])->id;
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
        $sql = "DROP TABLE IF EXISTS `{$db->prefix}dublin_core_extended_relationships`";
        $this->_db->query($sql);
    }
    
    private function _deleteElements()
    {
        // Delete all the elements and element texts.
        foreach ($this->_elements as $element) {
            if (!in_array($element['name'], $this->_dcElements)) {
                $this->_getElement($element['name'])->delete();
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