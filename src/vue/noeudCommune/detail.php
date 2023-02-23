Noeud routier :
    gid <?= $noeudCommune->getGid() ?>,
    id_rte <?= $noeudCommune->getId_rte500() ?>,
    nomCommune <?= $noeudCommune->getNomCommune() ?>
    long <?= $noeudCommune->getLongCommune() ?>
    lat <?= $noeudCommune->getLatCommune() ?>

<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<div id="map" style ="height:650px;width:620px;margin:auto;"></div>

<script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCNgiSeE--QYZtlP4qYMTDatGQrDXgql8M&callback=initMap&v=weekly"
        defer
></script>
<script >
    function initMap() {
        const myLatLng =  { lat: <?=$noeudCommune->getLatCommune()?> , lng:<?=$noeudCommune->getLongCommune()?> };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 13,
            center: myLatLng,
        });

        new google.maps.Marker({
            position: myLatLng,
            map,
            title: "<?= $noeudCommune->getNomCommune() ?>",
        });
    }

    window.initMap = initMap;


</script>

