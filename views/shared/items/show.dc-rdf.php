<?php echo '<?xml version="1.0"?>'; ?>

<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
 xmlns:dcterms="http://purl.org/dc/terms/">
<?php 
require_once 'ItemDcRdf.php';
$convert = new ItemDcRdf;
echo $convert->itemToDcRdf($item);
?>

</rdf:RDF>