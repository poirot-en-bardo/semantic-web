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

        function send1() {
            var agencyId = null;
            var cityId = null;
            var sendObject;
            newData = $("#formular");
            serializedData = newData.serialize(); //sau cu JSON.stringify?
            formValues = Object.fromEntries(new FormData($("#formular")[0]));
            $.getJSON("http://localhost:4000/agencies", function (agencies) {
                agencies.forEach(agency => {
                        if (agency.name == formValues.agency) {
                            agencyId = agency.id;
                        }
                    }
                )
            })
            $.getJSON("http://localhost:4000/cities", function (cities) {
                cities.forEach(city => {
                        if (city.name == formValues.city)
                            cityId = city.id;
                    }
                )
                $.getJSON("http://localhost:4000/greatestId", function (id){
                    postId = id.value + 1;
                    sendObject = {"id": postId, "agencyId": agencyId, "cityId": cityId, price: parseInt(formValues.price, 10)};
                    config = {
                        url: "http://localhost:4000/tours",
                        type: "POST",
                        data: JSON.stringify(sendObject),
                        contentType: "application/json",
                        succes: processResponse1
                    }
                    $.ajax(config);

                    $.ajax({
                        url: "http://localhost:4000/greatestId",
                        type: "PUT",
                        data: JSON.stringify({"value":postId}),
                        contentType: "application/json",
                        succes: function(result){
                            console.log(result);
                        }
                    })


                })





            })





        }

        function processResponse1(response) {
            console.log("Inserare date " + response);

        }

        function getData() {
            $.getJSON("http://localhost:4000/tours?_expand=agency&_expand=city", function (json) {
                for (i = 0; i <= json.length; i++) {
                    line = "<tr>" +
                        "<td>" + json[i].agency.name + "</td>" +
                        "<td>" + json[i].agency.phone + "</td>" +
                        "<td>" + json[i].city.name + "</td>" +
                        "<td>" + json[i].city.country + "</td>" +
                        "<td>" + json[i].price + "</td>" +
                        "</tr>";
                    tableBody = $("#tabel1 tbody");
                    tableBody.append(line);
                }
            });
        }
    </script>
</head>
<body>


<img src="autobar.jpeg" height="430" width="800">

<h2>Introduceti datele:</h2>

<form id="formular">
    Agentie <input type="text" name="agency"><br/>
    Oras <input type="text" name="city"><br/>
    Pret excursie <input type="number" name="price"><br/>
</form>

<div>
    <button class="sendButton" onclick="send1()">Trimite catre Serverul X</button>
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
    <button class="sendButton" onclick="trimite2()">Trimite catre Serverul Y</button>
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

