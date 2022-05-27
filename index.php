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
        //$data=json_decode($rezultatJSON)->results->bindings;
        $graf->addResource("Irina", "schema:knows", "Petru");
        $graf->addResource("Irina", "schema:knows", "Pavel");
        $graf->add("Irina", "varsta", "22");
        $client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/proiect/statements");
        print $client->insert($graf, "http://balicaprichici.ro#grafNou2");
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
                    line = addLine(selectedAgency?.name, selectedAgency?.phone, selectedCity?.name, selectedCity?.country,
                        response.price)
                    tableBody = $("#table1 tbody");
                    tableBody.append(line);

                    tours.push({"agencyId": selectedAgency.id, "cityId": selectedCity.id, "price": response.price});
                }
            });
        }


        function addLine(agname, agphone, ctname, ctcountry, price) {
            line = "<tr>" +
                "<td>" + agname + "</td>" +
                "<td>" + agphone + "</td>" +
                "<td>" + ctname + "</td>" +
                "<td>" + ctcountry + "</td>" +
                "<td>" + price + "</td>" +
                "</tr>";
            return line;
        }

        async function send2() {
            await Promise.all([graphAgencies(), graphCities(), graphTours()]);

            console.log(agencies2);
            console.log(cities2);
            console.log(tours2);
            $("#table2 > tbody:last").children( 'tr:not(:first)' ).remove();
            for (i = 0; i < tours2.length; i++) {
                joinQuery = JSON.stringify({
                    query: `{Tour(id: "${i}"){price Agency{name phone} City{name country}}}`
                })
                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: joinQuery,
                    contentType: "application/json",
                    success: function (response) {
                        console.log(response)
                        agency = response.data.Tour.Agency;
                        city = response.data.Tour.City;
                        line = addLine(agency?.name, agency?.phone, city?.name, city?.country, response.data.Tour?.price);
                        tableBody = $("#table2 tbody");
                        tableBody.append(line);
                    }
                })
            }
        }

        async function graphAgencies() {
            console.log("ag1")
            let allAgencies = JSON.stringify({
                query: `{_allAgenciesMeta{count}}`
            })
            $.ajax({
                url: "http://localhost:3000",
                type: "POST",
                data: allAgencies,
                contentType: "application/json",
                success: insertAgencies
            })

            return 1;
        }

        function insertAgencies(response) {
            console.log("ag2")
            agenciesNo = response.data._allAgenciesMeta.count;

            for (i = agenciesNo; i >= 0; i--) {
                deleteQuery = JSON.stringify({
                    query: `mutation {removeAgency(id: "${i}"){name}}`
                })
                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: deleteQuery,
                    contentType: "application/json"
                })
            }

            agencies2 = []
            for (i = 0; i < agencies.length; i++) {
                postQuery = JSON.stringify({
                    query: `mutation {createAgency(name:"${agencies[i].name}",
                                        phone:"${agencies[i].phone}") {id name phone}}`
                })

                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: postQuery,
                    contentType: "application/json",
                    success: function (response) {
                        agencies2.push(response.data.createAgency);
                    }
                });
            }
        }

        async function graphCities() {
            console.log("ct1")
            let allCities = JSON.stringify({
                query: `{_allCitiesMeta{count}}`
            })
            $.ajax({
                url: "http://localhost:3000",
                type: "POST",
                data: allCities,
                contentType: "application/json",
                success: insertCities
            })

            return 1;
        }

        function insertCities(response) {
            console.log("ct2")
            citiesNo = response.data._allCitiesMeta.count;

            for (i = citiesNo; i >= 0; i--) {
                deleteQuery = JSON.stringify({
                    query: `mutation {removeCity(id: "${i}"){name}}`
                })
                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: deleteQuery,
                    contentType: "application/json"
                })
            }

            cities2 = []
            for (i = 0; i < cities.length; i++) {
                postQuery = JSON.stringify({
                    query: `mutation {createCity(name:"${cities[i].name}",
                            country:"${cities[i].country}") {id name country}}`
                })

                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: postQuery,
                    contentType: "application/json",
                    success: function (response) {
                        cities2.push(response.data.createCity);
                    }
                });
            }
        }


        async function graphTours() {
            console.log("to1")
            let allTours = JSON.stringify({
                query: `{_allToursMeta{count}}`
            })
            $.ajax({
                url: "http://localhost:3000",
                type: "POST",
                data: allTours,
                contentType: "application/json",
                success: insertTours
            })

            return 1;
        }


        function insertTours(response) {
            console.log("to2")
            toursNo = response.data._allToursMeta.count;

            for (i = toursNo; i >= 0; i--) {
                deleteQuery = JSON.stringify({
                    query: `mutation {removeTour(id: "${i}"){id}}`
                })
                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: deleteQuery,
                    contentType: "application/json"
                })
            }

            tours2 = []
            for (i = 0; i < tours.length; i++) {
                postQuery = JSON.stringify({
                    query: `mutation {createTour(agency_id:"${tours[i].agencyId}",
                            city_id:"${tours[i].cityId}", price:${tours[i].price}) {agency_id city_id price}}`
                })

                $.ajax({
                    url: "http://localhost:3000",
                    type: "POST",
                    data: postQuery,
                    contentType: "application/json",
                    success: function (response) {
                        tours2.push(response.data.createTour);
                    }
                });
            }
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
                    line = addLine(tour.agency.name, tour.agency.phone, tour.city.name, tour.city.country, tour.price)
                    tableBody = $("#table1 tbody");
                    tableBody.append(line);

                    tours.push({"agencyId": tour.agency.id, "cityId": tour.city.id, "price": tour.price})
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
    <table id="table1">
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
    <table id="table2">
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
    <table id="table3">
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

