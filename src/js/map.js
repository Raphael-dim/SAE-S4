const map = new google.maps.Map(document.getElementById("map"), {
    center: {lat: 46.227638, lng: 2.213749},
    zoom: 6,
});

let markersArray = [];
let markerDepart = null;
let markerArrivee = null;

function addMarker(latLng) {
    let marker = new google.maps.Marker({
        map: map,
        position: latLng,
        // draggable: true
    });
    markersArray.push(marker);
    return marker;
}

function setMarkerDepart(marker) {
    if (markerDepart !== null) {
        markerDepart.setMap(null);
    }
    markerDepart = marker;
}

imageLocaliser = document.getElementsByClassName("localiser");

function localiser(pos) {
    let crd = pos.coords;
    let latitude = crd.latitude;
    let longitude = crd.longitude;
    let latLng = {lat: latitude, lng: longitude};
    let marker = new google.maps.Marker({
        map: map,
        position: latLng,
        // draggable: true
    });
    setMarkerDepart(marker)
    map.setCenter(latLng);
    map.setZoom(14);
    requete(latitude, longitude);
}

function requete(latitude, longitude) {
    let url = "chercherVilleCoor/" + encodeURIComponent(latitude) + "/" + encodeURIComponent(longitude);
    let request = new XMLHttpRequest();
    request.open("GET", url, true);
    request.addEventListener("load", function () {
        let data = JSON.parse(request.responseText);
        let name = data.map(element => element["nom_comm"]);
        setVilleDepart(name[0]);
    });
    request.send(null);
}

imageLocaliser[0].addEventListener("mousedown", function (event) {
    navigator.geolocation.getCurrentPosition(localiser);
})

map.addListener('click', function (e) {
    // addMarker(e.latLng);
});


function initMap(noeudDepart, noeudArrivee) {

    let LatLngDepart;
    let LatLngArrivee;

    if (noeudDepart !== null) {
        LatLngDepart = {lat: parseFloat(noeudDepart["lat"]), lng: parseFloat(noeudDepart["long"])};
        map.setCenter(LatLngDepart);
        setMarkerDepart(new google.maps.Marker({
            position: LatLngDepart,
            map,
            title: noeudDepart["nomCommune"],
        }));
    }
    if (noeudArrivee !== null) {
        LatLngArrivee = {lat: parseFloat(noeudArrivee["lat"]), lng: parseFloat(noeudArrivee["long"])};
        map.setCenter(LatLngArrivee);
        if (markerArrivee !== null) {
            markerArrivee.setMap(null);
        }
        markerArrivee = new google.maps.Marker({
            position: LatLngArrivee,
            map,
            title: noeudArrivee["nomCommune"],
        });
    }
    if (noeudArrivee !== null && noeudDepart !== null) {
        Latlng = {
            lat: parseFloat((Number(noeudDepart['lat']) + Number(noeudArrivee['lat'])) / 2),
            lng: parseFloat((Number(noeudDepart['long']) + Number(noeudArrivee['long'])) / 2)
        }
        map.setCenter(Latlng);


        let distance = distanceEntreDeuxPoints([parseFloat(Number(noeudDepart['lat'])), parseFloat(Number(noeudDepart['long']))],
            [parseFloat(Number(noeudArrivee['lat'])), parseFloat(Number(noeudArrivee['long']))]);


        if (distance < 0.5) {
            map.setZoom(20);
        } else if (distance < 1) {
            map.setZoom(17);
        } else if (distance < 5) {
            map.setZoom(13);
        } else if (distance < 20) {
            map.setZoom(12);
        } else if (distance < 50) {
            map.setZoom(10);
        } else if (distance < 100) {
            map.setZoom(9);
        } else if (distance < 200) {
            map.setZoom(7);
        } else if (distance < 300) {
            map.setZoom(7);
        } else if (distance < 400) {
            map.setZoom(7);
        } else {
            map.setZoom(6);
        }
    }
}


function plotTroncon(tabTroncon) {
    console.log(tabTroncon);

    tabTroncon.forEach(troncon => {
        for (let i = 0; i < troncon["geom"]["coordinates"].length - 1; i++) {

            let geom = troncon["geom"]["coordinates"];
            var LatLgnStart = {lat: geom[i][1], lng: geom[i][0]};
            var LatLgnEnd = {lat: geom[i + 1][1], lng: geom[i + 1][0]};

            console.log(LatLgnStart);
            var line = new google.maps.Polyline({
                path: [LatLgnStart, LatLgnEnd],
                strokeColor: "#00c4ff",
                strokeOpacity: 1.0,
                strokeWeight: 10,
                geodesic: true,
                map: map
            })
        }
    });
}

function distanceEntreDeuxPoints(latlng1, latlng2) {
    const [lat1, lon1] = latlng1;
    const [lat2, lon2] = latlng2;
    const R = 6371; // Rayon de la Terre en km
    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(deg2rad(lat1)) *
        Math.cos(deg2rad(lat2)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const d = R * c; // Distance en km
    return d;
}

function deg2rad(deg) {
    return deg * (Math.PI / 180);
}