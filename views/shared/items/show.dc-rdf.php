<?php echo '<?xml version="1.0"?>'; ?>
<!DOCTYPE rdf:RDF PUBLIC "-//DUBLIN CORE//DCMES DTD 2002/07/31//EN"
"http://dublincore.org/documents/2002/07/31/dcmes-xml/dcmes-xml-dtd.dtd">
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
xmlns:dcterms="http://purl.org/dc/terms/">
<?php 
require_once 'ItemDc.php';
$convert = new ItemDcRdf; 
echo $convert->recordToDcRdf($item); ?>
</rdf:RDF>
