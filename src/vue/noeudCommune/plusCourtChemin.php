<form action="calculer" method="post">
    <fieldset>
        <legend>Plus court chemin </legend>
        <div>
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="nomCommuneDepart_id">Nom de la commune de départ</label>
                <input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneDepart" id="nomCommuneDepart_id" required>
                <!-- <img id="loading" src="img/loading.gif"> -->
            <div class="autocompletion" id="autocompletionDepart"></div>
            </p>
        </div>
        <div>
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="nomCommuneArrivee_id">Nom de la commune de départ</label>
                <input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneArrivee" id="nomCommuneArrivee_id" required>
            </p>
            <!-- <img id="loading" src="img/loading.gif"> -->
            <div class="autocompletion" id="autocompletionArrivee"></div>
        </div>
        <div>
            <input type="hidden" name="XDEBUG_TRIGGER">
            <p>
                <input class="InputAddOn-field" type="submit" value="Calculer" />
            </p>
        </div>
    </fieldset>
</form>

<?php if (!empty($_POST)) { ?>
    <p>
        Le plus court chemin entre <?= $CommuneDepart->getNomCommune() ?> et <?= $CommuneArrivee->getNomCommune() ?> mesure <?= $distance ?>km.
    </p>

    <!-------------------------------------GOOGLE MAPS API---------------------------------------->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <div id="map" style="height:650px;width:620px;margin:auto;"></div>


    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCNgiSeE--QYZtlP4qYMTDatGQrDXgql8M&v=weekly"></script>
    <!-------------------------------------------------------------------------------------------->

    <!---------------------------------------INIT MAP--------------------------------------------->
    <script src="../ressources/js/map.js"></script>
    <script defer>
        let CommuneDepartJSON = <?= $CommuneDepart->toJson() ?>;
        let CommuneArriveeJSON = <?= $CommuneArrivee->toJson() ?>;
        initMap(CommuneDepartJSON, CommuneArriveeJSON);

        let tabTronconJSON = <?= json_encode($troncons) ?>;
        plotTroncon(tabTronconJSON);
    </script>
    <!-------------------------------------------------------------------------------------------->

<?php } ?>
<script src="../src/js/AutoCompletion.js" defer></script>