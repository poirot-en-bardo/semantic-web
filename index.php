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
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            getData();
        });

        let agencies;
        let cities;

        function send1() {
            let agencyId = null;
            let agencyPhone = "";
            let cityId = null;
            let country = "";
            let sendObject;
            formValues = Object.fromEntries(new FormData($("#formular")[0]));
            $.getJSON("http://localhost:4000/agencies", function (agencies) {
                agencies.forEach(agency => {
                        if (agency.name === formValues.agency) {
                            agencyId = agency.id;
                            agencyPhone = agency.phone;
                        }
                    }
                )
            })
            $.getJSON("http://localhost:4000/cities", function (cities) {
                cities.forEach(city => {
                        if (city.name === formValues.city) {
                            cityId = city.id;
                            country = city.country;
                        }
                    }
                )
                sendObject = {
                    "agencyId": agencyId,
                    "cityId": cityId,
                    price: parseInt(formValues.price, 10)
                };
                $.ajax({
                    url: "http://localhost:4000/tours",
                    type: "POST",
                    data: JSON.stringify(sendObject),
                    contentType: "application/json",
                    success: function (response) {
                        line = "<tr>" +
                            "<td>" + formValues.agency + "</td>" +
                            "<td>" + agencyPhone + "</td>" +
                            "<td>" + formValues.city + "</td>" +
                            "<td>" + country + "</td>" +
                            "<td>" + response.price + "</td>" +
                            "</tr>";
                        tableBody = $("#tabel1 tbody");
                        tableBody.append(line);
                    }
                });

            })
        }

        function processResponse1(response) {
        }

        function getData() {

            $.getJSON("http://localhost:4000/tours?_expand=agency&_expand=city", function (tours) {
               tours.forEach(tour => {
                   line = "<tr>" +
                       "<td>" + tour.agency.name + "</td>" +
                       "<td>" + tour.agency.phone + "</td>" +
                       "<td>" + tour.city.name + "</td>" +
                       "<td>" + tour.city.country + "</td>" +
                       "<td>" + tour.price + "</td>" +
                       "</tr>";
                   tableBody = $("#tabel1 tbody");
                   tableBody.append(line);
               })
            });

            $.getJSON("http://localhost:4000/agencies", function (items) {
                agencies = items;
                $.each(agencies, function (index, agency) {
                    var $option = $("<option/>", {
                        value: index,
                        text: agency.name
                    });
                    $('#agentii').append($option);
                });
            })

            $.getJSON("http://localhost:4000/cities", function (items) {
                cities = items;
                $.each(cities, function (index, city) {
                    var $option = $("<option/>", {
                        value: index,
                        text: city.name
                    });
                    $('#orase').append($option);
                });
            })
        }
    </script>
</head>
<body>


<img src="autobar.jpeg" height="430" width="800">

<h2>Introduceti datele:</h2>

<form id="formular">
    <label for="agentii">Selectati o agentie:</label>
    <select name="agentii" id="agentii"> </select> <br/>

    <label for="orase">Selectati un oras:</label>
    <select name="orase" id="orase"> </select> <br/>

    <label for="price">Pret excursie:</label>
    <input type="number" name="price"><br/>

    Agentie <input type="text" name="agency"><br/>
    Oras <input type="text" name="city"><br/>

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
            <th>Camp1</th>
            <th>Camp2</th>
            <th>Camp3</th>
            <th>Camp4</th>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>
</div>

</body>
</html>

