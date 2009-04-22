<?php
class ItemDcRdf
{
    protected $_elements;
    
    public function __construct()
    {
        $dces = new DublinCoreExtendedPlugin;
        $this->_elements = $dces->getElements();
    }
    
    public function itemToDcRdf(Item $item)
    {
        $xml = '<rdf:Description rdf:about="' . abs_item_uri($item) . '">';
        foreach ($this->_elements as $element) {
            $label = $element['label'];
            $name = $element['name'];
            if ($text = item('Dublin Core', $label, 'all')) {
                foreach ($text as $value) {
                    if (strlen($value) != 0) {
                        $xml .= "\n    <dcterms:$name><![CDATA[$value]]></dcterms:$name>" ;
                    }
                }
            }
        }
        $xml .= "\n</rdf:Description>";
        return $xml;
    }
}
