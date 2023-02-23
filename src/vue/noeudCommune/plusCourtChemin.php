<form action="" method="post">
    <fieldset>
        <legend>Plus court chemin </legend>
        <div style="overflow:auto; height:300px;">
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="nomCommuneDepart_id">Nom de la commune de départ</label>
                <input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneDepart" id="nomCommuneDepart_id" required>
            </p>
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="nomCommuneArrivee_id">Nom de la commune d'arrivé</label>
                <input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneArrivee" id="nomCommuneArrivee_id" required>
            </p>
        </div>
        <button onclick="test()" class="InputAddOn-field" style="width:100%;height: 100px">Ajouter une escale</button>
        <input type="hidden" name="XDEBUG_TRIGGER">
        <p>
            <input class="InputAddOn-field" type="submit" value="Calculer" />
        </p>
    </fieldset>
</form>

<script>
    //EN COURS DE DEV

    let nbEscale = 0;

    function test(){
        nbEscale++;
        let escaleHTML =
            '<p class="InputAddOn"> ' +
            '<label class="InputAddOn-item" for="nomCommuneEscale' + nbEscale + '_id">Nom de la commune d\'escale ' + nbEscale + '</label> ' +
            '<input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneEscale' + nbEscale + '" id="nomCommuneEscale' + nbEscale + '_id"> ' +
            '</p>';
        document.querySelector(".InputAddOn:last-of-type").insertAdjacentHTML('beforebegin',escaleHTML);
    }
</script>

<?php if (!empty($_POST)) { ?>
    <p>
        Le plus court chemin entre <?= $CommuneDepart->getNomCommune() ?> et <?= $CommuneArrivee->getNomCommune() ?> mesure <?= $distance ?>km.
    </p>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <div id="map" style ="height:650px;width:620px;margin:auto;"></div>

    <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCNgiSeE--QYZtlP4qYMTDatGQrDXgql8M&callback=initMap&v=weekly"
            defer
    ></script>
    <script >
        function initMap() {
            const LatLngDepart =  { lat: <?=$CommuneDepart->getLatCommune()?> , lng:<?=$CommuneDepart->getLongCommune()?> };
            const LatLngArrivee=  { lat: <?=$CommuneArrivee->getLatCommune()?> , lng:<?=$CommuneArrivee->getLongCommune()?> };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: LatLngDepart,
            });

            new google.maps.Marker({
                position: LatLngDepart,
                map,
                title: "<?= $CommuneDepart->getNomCommune() ?>",
            });

            new google.maps.Marker({
                position: LatLngArrivee,
                map,
                title: "<?= $CommuneArrivee->getNomCommune() ?>",
            });

            var line = new google.maps.Polyline({
                path: [LatLngDepart, LatLngArrivee],
                strokeColor: "#00c4ff",
                strokeOpacity: 1.0,
                strokeWeight: 10,
                geodesic: true,
                map: map
            });

        }

        window.initMap = initMap;
    </script>

<?php } ?>