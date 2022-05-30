<?php
require 'vendor/autoload.php';

$client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/grafexamen/statements");
$graf = new EasyRdf\Graph("http://balicaprichici.ro#grafAPI");
$prefixe = new EasyRdf\RdfNamespace();
$prefixe->setDefault("http://balicaprichici.ro#");


$received=file_get_contents("php://input");
$data = json_decode($received, true);
print_r( $data);

foreach ((array)$data as $value) {
    $activity = str_replace(' ', '_', $value['activity']);
    $graf->addResource($activity, "a", "schema:Activity");
    $graf->addResource($activity, "hasType", str_replace(' ','_',$value['type']));
    $graf->addResource($activity, "nrParticipants", str_replace(' ','_',$value['participants'].''));
    $graf->addResource($activity, "hasPrice", str_replace(' ','_',$value['price'].''));
}

$client->insert($graf, "http://balicaprichici.ro#grafAPI");

?>