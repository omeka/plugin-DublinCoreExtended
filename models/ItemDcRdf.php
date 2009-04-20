<?php
class ItemDcRdf
{
    public function recordToDcRdf($item)
    {      
        $dcElements = $item->getElementsBySetName('Dublin Core');
    
        $xml = "\n" . '<rdf:Description rdf:about="' . abs_item_uri($item) . '">';
        // Iterate throught the DCMES.
        foreach ($dcElements as $element) {
            $elementName = $element->name;
            if ($text = item('Dublin Core', $elementName, 'all')) {
                foreach ($text as $k => $v) {
                    if (!empty($v)) {
                        $xml .= "\n" . '<dcterms:' . strtolower($elementName) . '><![CDATA[' 
                            . $v . ']]></dcterms:' . strtolower($elementName) . '>';
                    }
                }
            }
        }
        $xml .= "\n" . '</rdf:Description>';
        return $xml;        
    }
}
