const map = new google.maps.Map(document.getElementById("map"), {
    center: {lat: -34.397, lng: 150.644},
    zoom: 13,
});

let markerDepart = null;
let markerArrivee = null;
let markers = [];


imageLocaliser = document.getElementsByClassName("localiser");

function localiser(pos) {

    let crd = pos.coords;
    let latitude = crd.latitude;

    let longitude = crd.longitude;
}

imageLocaliser[0].addEventListener("mousedown", function (event) {
    navigator.geolocation.getCurrentPosition(localiser);
})

function initMap(noeuds, i) {

    let LatLngNoeuds = [];

    noeuds.forEach(function (n) {
        if (n !== 0) {
            LatLngNoeuds.push({lat: parseFloat(n["long"]), lng: parseFloat(n["lat"])});
            map.setCenter(LatLngNoeuds[LatLngNoeuds.length-1]);
            if (markers[noeuds.indexOf(n)] !== undefined) {
                markers[noeuds.indexOf(n)].setMap(null);
                markers.pop();
            }
            markers.push(new google.maps.Marker({
                position: LatLngNoeuds[LatLngNoeuds.length-1],
                map,
                title: n["nomCommune"],
            }));
        }
    });
    if (markers.length === i) {
        Latlng = {
            lat: parseFloat((Number(noeuds[0]['long']) + Number(noeuds[noeuds.length-1]['long'])) / 2),
            lng: parseFloat((Number(noeuds[0]['lat']) + Number(noeuds[noeuds.length-1]['lat'])) / 2)
        }
        map.setCenter(Latlng);


        let distance = distanceEntreDeuxPoints([parseFloat(Number(noeuds[0]['lat'])), parseFloat(Number(noeuds[0]['long']))],
            [parseFloat(Number(noeuds[noeuds.length-1]['lat'])), parseFloat(Number(noeuds[noeuds.length-1]['long']))]);

        map.setZoom(Math.max(0.5,Math.min(20,140/distance)));
    }
}


function plotTroncon(tabTroncon) {

    tabTroncon.forEach(troncon => {
        for (let i = 0; i < troncon["geom"]["coordinates"].length - 1; i++) {

            let geom = troncon["geom"]["coordinates"];
            var LatLgnStart = {lat: geom[i][1], lng: geom[i][0]};
            var LatLgnEnd = {lat: geom[i + 1][1], lng: geom[i + 1][0]};

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