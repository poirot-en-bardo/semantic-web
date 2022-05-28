<!DOCTYPE html>
<html>
<head>
    <title>Da</title>
    <style>

        .sendButton {
            margin-top: 15px;
            margin-left: 60px;
            margin-bottom: 15px;
        }

        input {
            margin: 10px;
        }

        table, th, td {
            border: 1px solid;
        }
    </style>
    <?php
    function CreateGraphs()
    {
        require 'vendor/autoload.php';
        $client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/proiect/statements");
        $graf = new EasyRdf\Graph("http://balicaprichici.ro#grafAgencies");
        $prefixe = new EasyRdf\RdfNamespace();
        $prefixe->setDefault("http://balicaprichici.ro#");
        //$data=json_decode($rezultatJSON)->results->bindings;
        $graf->addResource("Irina", "schema:knows", "Petru");
        $graf->addResource("Irina", "schema:knows", "Pavel");
        $graf->add("Irina", "varsta", "22");
        $client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/proiect/statements");
        print $client->insert($graf, "http://balicaprichici.ro#grafNou2");
    }

    ?>


    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src = "script.js"></script>
</head>
<body>


<img src="autobar.jpeg" height="430" width="800">

<h2>Introduceti datele:</h2>

<form id="formular">
    <label for="agency">Selectati o agentie:</label>
    <select name="agency" id="agency"> </select> <br/>

    <label for="city">Selectati un oras:</label>
    <select name="city" id="city"> </select> <br/>

    <label for="price">Pret excursie:</label>
    <input type="number" name="price"><br/>

</form>

<div>
    <button class="sendButton" onclick="send1()">Trimite catre JSON Server</button>
</div>

<div>
    <h2>Date returnate:</h2>
    <table class="table" id="table1">
        <tr>
            <th>Agentie</th>
            <th>Telefon</th>
            <th>Oras</th>
            <th>Tara</th>
            <th>Pret excursie</th>
        </tr>
    </table>
</div>
<div>
    <button class="sendButton" onclick="send2()">Trimite catre GraphQL Server</button>
</div>

<div>
    <table class="table" id="table2">
        <tr>
            <th>Agentie</th>
            <th>Telefon</th>
            <th>Oras</th>
            <th>Tara</th>
            <th>Pret excursie</th>
        </tr>
    </table>
</div>

<div>
    <button class="sendButton" onclick="trimite3()">Trimite catre RDF4J Server</button>
</div>

<div>
    <table class="table" id="table3">
        <tr>
            <th>Agentie</th>
            <th>Telefon</th>
            <th>Oras</th>
            <th>Tara</th>
            <th>Pret excursie</th>
        </tr>
    </table>
</div>

<div>
    <button class="sendButton" onclick="apiCall()">API Call</button>
</div>

<div>
    API Random Activities
    <table class="table" id="table4">
        <tr>
            <th>Activity</th>
            <th>Type</th>
            <th>Participants</th>
            <th>Price</th>
        </tr>
    </table>
</div>

</body>
</html>

