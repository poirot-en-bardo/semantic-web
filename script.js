$(document).ready(function () {
    getData();
});

let agencies = [];
let cities = [];
let tours = [];
let agencies2 = [];
let cities2 = [];
let tours2 = [];
const json_link = "http://localhost:4000"
const graph_link = "http://localhost:3000"
const rdf_link = "php.php"
const api_link = "http://www.boredapi.com/api/activity/"


function getData() {

    $.getJSON(json_link + "/tours?_expand=agency&_expand=city", function (items) {
        items.forEach(tour => {
            line = addLine(tour.agency.name, tour.agency.phone, tour.city.name, tour.city.country, tour.price)
            tableBody = $("#table1 tbody");
            tableBody.append(line);
            tours.push({"agencyId": tour.agency.id, "cityId": tour.city.id, "price": tour.price})
        })
    });

    $.getJSON(json_link + "/agencies", function (items) {
        agencies = items;
        $.each(agencies, function (index, agency) {
            var $option = $("<option/>", {
                value: agency.id,
                text: agency.name
            });
            $('#agency').append($option);
        });
    })

    $.getJSON(json_link + "/cities", function (items) {
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

function send1() {
    let selectedAgency = null;
    let selectedCity = null;
    let sendObject;
    let formValues = Object.fromEntries(new FormData($("#formular")[0]));

    if (formValues.price == "") {
        alert("Va rugam sa introduceti un pret");
        return false;
    }

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
        url: json_link + "/tours",
        type: "POST",
        data: JSON.stringify(sendObject),
        contentType: "application/json",
        success: function (response) {
            let line = addLine(selectedAgency?.name, selectedAgency?.phone, selectedCity?.name, selectedCity?.country,
                response.price)
            let tableBody = $("#table1 tbody");
            tableBody.append(line);

            tours.push({"agencyId": selectedAgency.id, "cityId": selectedCity.id, "price": response.price});
        }
    });
    if (formValues.price == "") {
        alert("Va rugam sa introduceti un pret");
        return false;
    }
}

async function send2() {
    await Promise.all([graphAgencies(), graphCities(), graphTours()]);

    $("#table2 > tbody:last").children('tr:not(:first)').remove();
    for (i = 0; i < tours2.length; i++) {
        joinQuery = JSON.stringify({
            query: `{Tour(id: "${i}"){price Agency{name phone} City{name country}}}`
        })
        $.ajax({
            url: graph_link,
            type: "POST",
            data: joinQuery,
            contentType: "application/json",
            success: function (response) {
                let agency = response.data.Tour.Agency;
                let city = response.data.Tour.City;
                line = addLine(agency?.name, agency?.phone, city?.name, city?.country, response.data.Tour?.price);
                tableBody = $("#table2 tbody");
                tableBody.append(line);
            }
        })
    }
}

function requestGraph(data) {
    return new Promise(resolve => {
        $.ajax({
            url: graph_link,
            type: "POST",
            data: JSON.stringify(data),
            contentType: "application/json",
            success: function (response) {
                resolve(response)
            }
        });
    })
}

async function graphAgencies() {
    return new Promise((resolve) => {
        let allAgencies = JSON.stringify({
            query: `{_allAgenciesMeta{count}}`
        })
        $.ajax({
            url: graph_link,
            type: "POST",
            data: allAgencies,
            contentType: "application/json",
            success: async function (response) {
                await insertAgencies(response)
                resolve()
            }
        })
    })
}

async function insertAgencies(response) {
    let agenciesNo = response.data._allAgenciesMeta.count;
    let promises = []

    async function deleteAg(i) {
        await requestGraph({
            query: `mutation {removeAgency(id: "${i}"){name}}`
        })
    }

    for (i = agenciesNo; i >= 0; i--) {
        promises.push(deleteAg(i))
    }
    await Promise.all(promises);

    promises = []
    agencies2 = []

    async function insert(agency) {

        let response = await requestGraph({
            query: `mutation {createAgency(name:"${agency.name}",
                                        phone:"${agency.phone}") {id name phone}}`
        })
        agencies2.push(response.data.createAgency)
    }

    promises = agencies.map((agency, index) => insert(agency))
    await Promise.all(promises)
}


async function graphCities() {
    return new Promise((resolve) => {
        let allCities = JSON.stringify({
            query: `{_allCitiesMeta{count}}`
        })
        $.ajax({
            url: graph_link,
            type: "POST",
            data: allCities,
            contentType: "application/json",
            success: async function (response) {
                await insertCities(response)
                resolve()
            }
        })
    })
}

async function insertCities(response) {
    let citiesNo = response.data._allCitiesMeta.count;
    let promises = []

    async function deleteCity(i) {
        await requestGraph({
            query: `mutation {removeCity(id: "${i}"){name}}`
        })
    }

    for (i = citiesNo; i >= 0; i--) {
        promises.push(deleteCity(i))
    }
    await Promise.all(promises);

    promises = []
    cities2 = []

    async function insert(city) {

        let response = await requestGraph({
            query: `mutation {createCity(name:"${city.name}",
                                        country:"${city.country}") {id name country}}`
        })
        cities2.push(response.data.createCity)
    }

    promises = cities.map((city, index) => insert(city))
    await Promise.all(promises)
}


async function graphTours() {
    return new Promise(resolve => {
        let allTours = JSON.stringify({
            query: `{_allToursMeta{count}}`
        })
        $.ajax({
            url: graph_link,
            type: "POST",
            data: allTours,
            contentType: "application/json",
            success: async function (response) {
                await insertTours(response);
                resolve();
            }
        })
    })
}


async function insertTours(response) {
    let toursNo = response.data._allToursMeta.count;
    let promises = []

    async function deleteTour(i) {
        await requestGraph({
            query: `mutation {removeTour(id: "${i}"){id}}`
        })
    }

    for (i = toursNo; i >= 0; i--) {
        promises.push(deleteTour(i))
    }
    await Promise.all(promises);

    promises = []
    tours2 = []

    async function insert(tour) {
        let response = await requestGraph({
            query: `mutation {createTour(agency_id: "${tour.agencyId}",
                                        city_id: "${tour.cityId}", price: ${tour.price}) {agency_id city_id price}}`
        })
        tours2.push(response.data.createTour)
    }

    promises = tours.map((tour, index) => insert(tour))
    await Promise.all(promises)
}

async function apiHandler() {
    let promises = []
    let data = []

    async function apiCall() {
        return new Promise(resolve => {
            $.ajax({
                    url: api_link,
                    type: "GET",
                    contentType: "application/json",
                    success: function (response) {
                        data.push(response)
                        line = "<tr>" +
                            "<td>" + response.activity + "</td>" +
                            "<td>" + response.type + "</td>" +
                            "<td>" + response.participants + "</td>" +
                            "<td>" + response.price + "</td>" +
                            "</tr>";
                        tableBody = $("#table4 tbody");
                        tableBody.append(line);
                        resolve(response);
                    }
                }
            )
        })
    }

    for (i = 0; i < 2; i++) {
        promises.push(apiCall());
    }
    await Promise.all(promises);
    console.log(data)
    $.ajax({
        url: "api.php",
        type: "POST",
        data: JSON.stringify(data),
        contentType: "application/json",
        success: function (response) {
            console.log(response)
        }
    })
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

function send3() {
    $("#table3 > tbody:last").children('tr:not(:first)').remove();
    $.ajax({
        url: rdf_link,
        type: "GET",
        dataType: 'JSON',
        contentType: "application/json",
        success: function (response) {
            var len = response.length;
            response.tour.forEach(tour => {
                line = addLine(tour.agentie.substr(25), tour.telefon.substr(25), tour.oras.substr(25), tour.tara.substr(25), tour.pret.substr(25));
                tableBody = $("#table3 tbody");
                tableBody.append(line);
            })
        }
    })
}
