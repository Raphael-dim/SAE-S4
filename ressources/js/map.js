
const map = new google.maps.Map(document.getElementById("map"), {
    center: {lat: -34.397, lng: 150.644},
    zoom: 13,
});

function initMap(noeudDepart,noeudArrivee) {

    const LatLngDepart =  { lat: parseFloat(noeudDepart["lat"]) , lng:  parseFloat(noeudDepart["long"])};
    const LatLngArrivee=  { lat: parseFloat(noeudArrivee["lat"]) , lng: parseFloat(noeudArrivee["long"]) };

    map.setCenter(LatLngDepart);


    new google.maps.Marker({
        position: LatLngDepart,
        map,
        title: noeudDepart["nomCommune"],
    });

    new google.maps.Marker({
        position: LatLngArrivee,
        map,
        title: noeudArrivee["nomCommune"],
    });

}


function plotTroncon(tabTroncon){
    console.log(tabTroncon);

    tabTroncon.forEach(troncon=>{
        for (let i = 0;i<troncon["geom"]["coordinates"].length-1;i++){

            let geom = troncon["geom"]["coordinates"];
            var LatLgnStart = { lat :geom[i][1], lng : geom[i][0] };
            var LatLgnEnd = { lat : geom[i+1][1], lng : geom[i+1][0] };


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
