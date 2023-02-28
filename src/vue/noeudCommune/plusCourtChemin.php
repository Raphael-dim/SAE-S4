<form action="" method="post">
    <fieldset>
        <legend>Plus court chemin </legend>
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="nomCommuneDepart_id">Nom de la commune de départ</label>
            <input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneDepart" id="nomCommuneDepart_id" required>
        </p>
        <p class="InputAddOn">
            <label class="InputAddOn-item" for="nomCommuneArrivee_id">Nom de la commune de départ</label>
            <input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneArrivee" id="nomCommuneArrivee_id" required>
        </p>
        <input type="hidden" name="XDEBUG_TRIGGER">
        <p>
            <input class="InputAddOn-field" type="submit" value="Calculer" />
        </p>
    </fieldset>
</form>

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
            const LatLngDepart =  { lat: <?=$noeudDepart->getLatNoeud()?> , lng:<?=$noeudDepart->getLongNoeud()?> };
            const LatLngArrivee=  { lat: <?=$noeudArrivee->getLatNoeud()?> , lng:<?=$noeudArrivee->getLongNoeud()?> };

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

            <?php foreach ($troncons as $troncon){?>

                var LatLgnStart = { lat : <?=$troncon->getLatStart() ?>, lng : <?=$troncon->getLongStart() ?> };
                var LatLgnEnd = { lat : <?=$troncon->getLatEnd() ?>, lng : <?=$troncon->getLongEnd() ?> };
                var line = new google.maps.Polyline({
                    path: [LatLgnStart, LatLgnEnd],
                    strokeColor: "#00c4ff",
                    strokeOpacity: 1.0,
                    strokeWeight: 10,
                    geodesic: true,
                    map: map
                });
            <?php }?>
        }

        window.initMap = initMap;
    </script>
<?php } ?>