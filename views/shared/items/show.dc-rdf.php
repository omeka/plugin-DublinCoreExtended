<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:dcterms="http://dublincore.org/documents/2012/06/14/dcmi-terms/?v=terms">
<?php
$itemDcRdf = new Output_ItemDcRdf;
echo $itemDcRdf->itemToDcRdf($item);
?>
</rdf:RDF>
