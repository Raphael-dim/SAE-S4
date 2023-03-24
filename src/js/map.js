const map = new google.maps.Map(document.getElementById("map"), {
    center: {lat: -34.397, lng: 150.644},
    zoom: 13,
});

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
    markers.map(m => m.setMap(null));
    markers = [];

    let LatLngNoeuds = [];

    noeuds.forEach(function (n) {
        if (n !== 0) {
            LatLngNoeuds.push({lat: parseFloat(n["long"]), lng: parseFloat(n["lat"])});
            map.setCenter(LatLngNoeuds[LatLngNoeuds.length-1]);

            markers.push(new google.maps.Marker({
                position: LatLngNoeuds[LatLngNoeuds.length-1],
                map,
                title: n["nomCommune"],
            }));
        }
    });
    console.log(markers);
    if (markers.length === i) {

        Latlng = {
            lat: noeuds.map(n => n['long']).reduce((a, b)=> Number(a)+Number(b), 0)/markers.length,
            lng: noeuds.map(n => n['lat']).reduce((a, b)=> Number(a)+Number(b), 0)/markers.length
        }

        map.setCenter(Latlng);

        console.log(noeuds.map(n => n).reduce((a, b)=> distanceDeuxPoints(parseFloat(a['lat']),parseFloat(a['long']))+distanceDeuxPoints(parseFloat(b['lat']),parseFloat(b['long'])), 0));
        let distance = noeuds.map(n => n).reduce((a, b)=> distanceDeuxPoints(parseFloat(a['lat']),parseFloat(a['long']))+distanceDeuxPoints(parseFloat(b['lat']),parseFloat(b['long'])), 0);

        /*let distance = distanceDeuxPoints([parseFloat(Number(noeuds[0]['lat'])), parseFloat(Number(noeuds[0]['long']))],
            [parseFloat(Number(noeuds[noeuds.length-1]['lat'])), parseFloat(Number(noeuds[noeuds.length-1]['long']))]);*/

        map.setZoom(Math.max(1,Math.min(20,140/distance)));
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

function distanceDeuxPoints(latlng1, latlng2) {
    const [lat1, lon1] = latlng1;
    const [lat2, lon2] = latlng2;
    const R = 6371; // Rayon de la Terre en km
    const dLat = deg2rad(lat2 - lat1);
    const dLon = deg2rad(lon2 - lon1);
    const a =
        Math.pow(Math.sin(dLat / 2),2) +
        Math.cos(deg2rad(lat1)) *
        Math.cos(deg2rad(lat2)) *
        Math.pow(Math.sin(dLon / 2),2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const d = R * c; // Distance en km
    return d;
}

function deg2rad(deg) {
    return deg * (Math.PI / 180);
}