const map = new google.maps.Map(document.getElementById("map"), {
    center: {lat: 46.227638, lng: 2.213749},
    zoom: 6,
});

let markers = [];

function initMap(noeuds, nomVille) {
    markers.map(m => m.setMap(null));
    markers = [];
    let LatLngNoeuds = [];
    noeuds.forEach(function (n) {
        if (n !== 0) {
            LatLngNoeuds.push({lat: parseFloat(n["lat"]), lng: parseFloat(n["long"])});
            // map.setCenter(LatLngNoeuds[LatLngNoeuds.length - 1]);
            // map.setZoom(14);
            markers.push(new google.maps.Marker({
                position: LatLngNoeuds[LatLngNoeuds.length - 1],
                map,
                title: nomVille,
            }));
        }
    });
    if (markers.length === noeuds.length) {

        Latlng = {
            lat: noeuds.map(n => n['lat']).reduce((a, b) => Number(a) + Number(b), 0) / markers.length,
            lng: noeuds.map(n => n['long']).reduce((a, b) => Number(a) + Number(b), 0) / markers.length
        }

        map.setCenter(Latlng);

        let distance = distanceDeuxPoints([Math.max.apply(Math, noeuds.map(function (n) {
                return n['lat'];
            })), Math.max.apply(Math, noeuds.map(function (n) {
                return n['long'];
            }))],
            [Math.min.apply(Math, noeuds.map(function (n) {
                return n['lat'];
            })), Math.min.apply(Math, noeuds.map(function (n) {
                return n['long'];
            }))]);

        const bounds = new google.maps.LatLngBounds();
        const point1 = [markers[0].position.lat(), markers[0].position.lng()];
        const point2 = [markers[1].position.lat(), markers[1].position.lng()];
        bounds.extend(new google.maps.LatLng(point1[0], point1[1]));
        bounds.extend(new google.maps.LatLng(point2[0], point2[1]));
        let i = 2
        while (markers[i] != null) {
            const point = [markers[i].position.lat(), markers[i].position.lng()];
            bounds.extend(new google.maps.LatLng(point[0], point[1]));
            i++;
        }
        map.fitBounds(bounds);
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
        Math.pow(Math.sin(dLat / 2), 2) +
        Math.cos(deg2rad(lat1)) *
        Math.cos(deg2rad(lat2)) *
        Math.pow(Math.sin(dLon / 2), 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const d = R * c; // Distance en km
    return d;
}

function deg2rad(deg) {
    return deg * (Math.PI / 180);
}

function supprimerMarker(nomVilleASupprimer) {
    console.log(nomVilleASupprimer)
    // markers[i].setMap(null);
    // markers.shift(markers[i]);
}