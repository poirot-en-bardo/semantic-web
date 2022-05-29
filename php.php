<?php
require 'vendor/autoload.php';


$client=new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/98/statements");
$graf=new EasyRdf\Graph("http://balicaprichici.ro#grafAgentii");
$prefixe=new EasyRdf\RdfNamespace();
$prefixe->setDefault("http://balicaprichici.ro#");
\EasyRdf\RdfNamespace::set('ag', "http://balicaprichiciag.ro#");

$spaceID = "mt0pmhki5db7";
$accessToken = "8c7dbd270cb98e83f9d8d57fb8a2ab7bac9d7501905fb013c69995ebf1b2a719";

$endpoint = "http://localhost:3000/";

$query = "query {
  allTours{ 
  id
  price 
  Agency {id name phone} 
  City {id name country}
  }
}
";

$data = array ('query' => $query);
$data = http_build_query($data);

$options = array(
  'http' => array(
    //'header'  => sprintf("Authorization: Bearer %s",$accessToken). "Content-Type: application/x-www-form-urlencoded\r\n",
	'header' => "Content-Type: application/x-www-form-urlencoded\r\n", 
            "Content-Length: ".strlen($query)."\r\n".
            "User-Agent:MyAgent/1.0\r\n",
    'method'  => 'POST',
    'content' => $data
  )
);

$context  = stream_context_create($options);
$result = file_get_contents(sprintf($endpoint, $spaceID), false, $context);

if ($result === FALSE) {echo "cf";} 
$json = json_decode($result, true);
$json=$json['data']['allTours'];
var_dump($json);
//$arrLength = count($json);
//echo $arrLength.'';
//for($i=0;$i<$json.length();$i++)
//{
//	echo $i.'';
//}
foreach($json as $field => $value)
{
	$name = str_replace(' ', '', $value['Agency']['name']);	
	$graf->addResource($name.'',"a","schema:TravelAgency");
	$graf->addResource($name.'',"schema:identifier","ag:a".$value['Agency']['id'].'');
	$graf->addResource($name.'',"nr_tel",''.$value['Agency']['phone'].'');
}
$client->insert($graf,"http://balicaprichici.ro#grafAgentii");
$graf=new EasyRdf\Graph("http://balicaprichici.ro#grafOrase");
$prefixe=new EasyRdf\RdfNamespace();
$prefixe->setDefault("http://balicaprichici.ro#");
\EasyRdf\RdfNamespace::set('ct', "http://balicaprichicict.ro#");

foreach ($json as $field => $value) {
	$name = str_replace(' ', '', $value['City']['name']);
	$graf->addResource($name.'',"a","schema:City");
	$graf->addResource($name.'',"schema:identifier","ct:c".$value['City']['id'].'');
	$graf->addResource($name.'',"is_in",str_replace(' ', '', $value['City']['country']));
	$graf->addResource(str_replace(' ', '', $value['City']['country']),"a","schema:Country");
}
 $client->insert($graf,"http://balicaprichici.ro#grafOrase");
 
$graf=new EasyRdf\Graph("http://balicaprichici.ro#grafExcursii");
$prefixe=new EasyRdf\RdfNamespace();
$prefixe->setDefault("http://balicaprichici.ro#");
\EasyRdf\RdfNamespace::set('ct', "http://balicaprichicict.ro#");
\EasyRdf\RdfNamespace::set('ag', "http://balicaprichiciag.ro#");
  
 foreach($json as $field=> $value)
{	
	$graf->addResource("t".$value['id'].'',"a","schema:Travel");
	$graf->addResource("t".$value['id'].'',"este_organizat_de","ag:".$value['Agency']['id']);
	$graf->addResource("t".$value['id'].'',"este_organizat_in","ct:".$value['City']['id']);
	$graf->addResource("t".$value['id'].'',"pret",$value['price'].'');
}
print $client->insert($graf,"http://balicaprichici.ro#grafExcursii");


 
 
//$interogare="SELECT ?subiect ?nume WHERE {?subiect rdfs:label ?nume}";
?>