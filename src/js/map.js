
const map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: -34.397, lng: 150.644 },
    zoom: 13,
});

let markerDepart;
let markerArrivee;

function initMap(noeudDepart, noeudArrivee) {

    let LatLngDepart;
    let LatLngArrivee;

    console.log(markerDepart);

    if (noeudDepart !== null) {
        LatLngDepart = { lat: parseFloat(noeudDepart["lat"]), lng: parseFloat(noeudDepart["long"]) };
        map.setCenter(LatLngDepart);
        markerDepart = new google.maps.Marker({
            position: LatLngDepart,
            map,
            title: noeudDepart["nomCommune"],
        });
    }
    if (noeudArrivee !== null) {
        LatLngArrivee = { lat: parseFloat(noeudArrivee["lat"]), lng: parseFloat(noeudArrivee["long"]) };
        map.setCenter(LatLngArrivee);
        markerDepart = new google.maps.Marker({
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
        map.setZoom(7);
    }
}


function plotTroncon(tabTroncon) {
    console.log(tabTroncon);

    tabTroncon.forEach(troncon => {
        for (let i = 0; i < troncon["geom"]["coordinates"].length - 1; i++) {

            let geom = troncon["geom"]["coordinates"];
            var LatLgnStart = { lat: geom[i][1], lng: geom[i][0] };
            var LatLgnEnd = { lat: geom[i + 1][1], lng: geom[i + 1][0] };

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
