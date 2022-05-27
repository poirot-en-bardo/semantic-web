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
    </style>
    <?php
    function CreateGraphs()
    {
        require 'vendor/autoload.php';
        $client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/proiect/statements");
        $graf = new EasyRdf\Graph("http://balicaprichici.ro#grafAgencies");
        $prefixe = new EasyRdf\RdfNamespace();
        $prefixe->setDefault("http://balicaprichici.ro#");
        $graf->addResource("Irina", "schema:knows", "Petru");
        $graf->addResource("Irina", "schema:knows", "Pavel");
        $graf->add("Irina", "varsta", "22");
        $client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/grafuri/statements");
        print $client->insert($graf, "http://buchmann.ro#grafNou2");
    }

    ?>


    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            getData();
        });

        let agencies = [];
        let cities = [];
        let tours = [];
        let agencies2 = [];
        let cities2 = [];
        let tours2 = [];

        function send1() {
            let selectedAgency = null;
            let selectedCity = null;
            let sendObject;
            let formValues = Object.fromEntries(new FormData($("#formular")[0]));

            agencies.forEach(agency => {
                if (agency.id == formValues.agency) {
                    selectedAgency = agency;
                }
            })
            cities.forEach(city => {
                if (city.id == formValues.city)
                    selectedCity = city;
            })


            sendObject = {
                "agencyId": selectedAgency.id,
                "cityId": selectedCity.id,
                price: parseInt(formValues.price, 10)
            };
            $.ajax({
                url: "http://localhost:4000/tours",
                type: "POST",
                data: JSON.stringify(sendObject),
                contentType: "application/json",
                success: function (response) {
                    line = "<tr>" +
                        "<td>" + selectedAgency?.name + "</td>" +
                        "<td>" + selectedAgency?.phone + "</td>" +
                        "<td>" + selectedCity?.name + "</td>" +
                        "<td>" + selectedCity?.country + "</td>" +
                        "<td>" + response.price + "</td>" +
                        "</tr>";
                    tableBody = $("#tabel1 tbody");
                    tableBody.append(line);

                    tours.push({"agencyId": selectedAgency.id, "cityId": selectedCity.id, "price": response.price});
                }
            });
        }

        async function send2() {
           const [a,c,t] = await Promise.all([insertAgencies(), insertCities(), insertTours()]);
           //inserare date in tabel
        }

        async function insertAgencies(){
            for (i = 1; i < agencies.length; i++) {
                obiectInterogare = {query: `mutation {createAgency(name:"${agencies[i].name}", phone:"${agencies[i].phone}") {id name phone}}`}
                textInterogare = JSON.stringify(obiectInterogare)

                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: textInterogare,
                    contentType: "application/json",
                    success: function (response) {
                        agencies2.push(response.data.createAgency);
                    }
                });
            }
            return 1;
        }

        async function insertCities(){
            for (i = 1; i < cities.length; i++) {
                obiectInterogare = {query: `mutation {createCity(name:"${cities[i].name}",
                country:"${cities[i].country}") {id name country}}`}
                textInterogare = JSON.stringify(obiectInterogare)

                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: textInterogare,
                    contentType: "application/json",
                    success: function(response) {
                        cities2.push(response.data.createCity);
                    }
                });
            }
            return 1;
        }

        async function insertTours(){
            for(i = 1; i < tours.length; i++) {
                obiectInterogare = {query: `mutation {createTour(agency_id:"${tours[i].agencyId}",
                city_id:"${tours[i].cityId}", price:${tours[i].price}) {agency_id city_id price}}`}
                textInterogare = JSON.stringify(obiectInterogare)

                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: textInterogare,
                    contentType: "application/json",
                    success: function(response) {
                        tours2.push(response.data.createTour);
                    }
                });
            }
            return 1;
        }





        function procesareRaspuns(raspuns) {
            // console.log(raspuns);
            // obiectInterogare = {query: "{Agency(id: 3){name phone}}"}
            // textInterogare = JSON.stringify(obiectInterogare)
            //
            // $.ajax({
            //     url: "http://localhost:3000",
            //     type: "POST",
            //     data: textInterogare,
            //     contentType: "application/json",
            //     success: function(raspuns){
            //         console.log(raspuns);
            //     }
            // })

//             textDeInserat = "<h1>Notele la cursul " + raspuns.data.Course.title + " </h1>"
//             $("#spatiuRezervat").append(textDeInserat)
// // am inserat intai un titlu ce include denumirea cursului
// // apoi dedesubt realizam un for each (in sintaxa JQuery!) ce va apela functia afisareText pentru fiecare nota returnata
//             inregistrari = raspuns.data.Course.Grades
//             $.each(inregistrari, afisareText)
        }

        // function afisareText(indice, inregistrare) {
        //     textDeInserat = "Studentul " + inregistrare.Student.name + " a luat nota " + inregistrare.grade + "<br/>"
        //     $("#spatiuRezervat").append(textDeInserat)
        //
        // }

        function getData() {

            $.getJSON("http://localhost:4000/tours?_expand=agency&_expand=city", function (items) {
                items.forEach(tour => {
                    line = "<tr>" +
                        "<td>" + tour.agency.name + "</td>" +
                        "<td>" + tour.agency.phone + "</td>" +
                        "<td>" + tour.city.name + "</td>" +
                        "<td>" + tour.city.country + "</td>" +
                        "<td>" + tour.price + "</td>" +
                        "</tr>";
                    tableBody = $("#tabel1 tbody");
                    tableBody.append(line);

                    tours.push({"agencyId": tour.agency.id, "cityId":tour.city.id, "price":tour.price})
                })
            });

            $.getJSON("http://localhost:4000/agencies", function (items) {
                agencies = items;
                $.each(agencies, function (index, agency) {
                    var $option = $("<option/>", {
                        value: agency.id,
                        text: agency.name
                    });
                    $('#agency').append($option);
                });
            })

            $.getJSON("http://localhost:4000/cities", function (items) {
                cities = items;
                $.each(cities, function (index, city) {
                    var $option = $("<option/>", {
                        value: city.id,
                        text: city.name
                    });
                    $('#city').append($option);
                });
            })
        }
    </script>
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
    <table id="tabel1">
        <tr>
            <th>Agentie</th>
            <th>Telefon</th>
            <th>Oras</th>
            <th>Tara</th>
            <th>Pret excursie</th>
        </tr>
        <tr></tr>
    </table>
</div>
<div>
    <button class="sendButton" onclick="send2()">Trimite catre Serverul Y</button>
</div>

<div>
    <table id="tabel2">
        <tr>
            <th>Agentie</th>
            <th>Telefon</th>
            <th>Oras</th>
            <th>Tara</th>
            <th>Pret excursie</th>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>

<div>
    <button class="sendButton" onclick="trimite3()">Trimite catre Serverul Z</button>
</div>

<div>
    <table>
        <tr>
            <th>Agentie</th>
            <th>Telefon</th>
            <th>Oras</th>
            <th>Tara</th>
            <th>Pret excursie</th>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>

</body>
</html>

