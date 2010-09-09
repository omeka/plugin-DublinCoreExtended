<?php
require DC_EXTENDED_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'DublinCoreExtendedPlugin.php';

class DublinCoreExtended_InstallerTest extends Omeka_Test_AppTestCase
{   
    public function testElementsAreInstalled()
    {
        DublinCoreExtendedPlugin::install();
        
        // Get list of additional elements that should be installed.
        require DC_EXTENDED_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'elements.php';
        
        $table = get_db()->getTable('Element');
        
        foreach ($elements as $element) {
            $name = $element['label'];
            $installedElement = $table->findByElementSetNameAndElementName('Dublin Core', $name);
            $this->assertTrue($installedElement instanceof Element);
            if (isset($element['description'])) {
                $this->assertEquals($element['description'], $installedElement->description);
            }
        }
    }
    
    public function testOrginialElementsRemain()
    {
        $originalElements = array('Title', 'Subject', 'Description', 
                                 'Creator', 'Source', 'Publisher', 
                                 'Date', 'Contributor', 'Rights', 
                                 'Relation', 'Format', 'Language', 
                                 'Type', 'Identifier', 'Coverage');
    
        DublinCoreExtendedPlugin::install();
        DublinCoreExtendedPlugin::uninstall();
        
        $table = get_db()->getTable('Element');
        
        foreach ($originalElements as $name) {
            $installedElement = $table->findByElementSetNameAndElementName('Dublin Core', $name);
            $this->assertTrue($installedElement instanceof Element);
        }
    }
}
