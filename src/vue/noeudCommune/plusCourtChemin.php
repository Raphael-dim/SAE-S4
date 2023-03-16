<form action="calculer" method="post">
    <fieldset>
        <legend>Plus court chemin </legend>
        <div id="autocompletionDepartDiv">
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="nomCommuneDepart_id">Nom de la commune de départ</label>
                <input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneDepart" id="nomCommuneDepart_id" required>
                <!-- <img id="loading" src="img/loading.gif"> -->
            <div class="autocompletion hidden" id="autocompletionDepart"></div>
            </p>
        </div>
        <div id="autocompletionArriveDiv">
            <p class="InputAddOn">
                <label class="InputAddOn-item" for="nomCommuneArrivee_id">Nom de la commune d'arrivée</label>
                <input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneArrivee" id="nomCommuneArrivee_id" required>
            </p>
            <!-- <img id="loading" src="img/loading.gif"> -->
            <div class="autocompletion hidden" id="autocompletionArrivee"></div>
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
    <p id = "loading">
      loading
    </p>
    <!-------------------------------------GOOGLE MAPS API---------------------------------------->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <div id="map" style="height:650px;width:620px;margin:auto;"></div>


    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCNgiSeE--QYZtlP4qYMTDatGQrDXgql8M&v=weekly"></script>
    <!-------------------------------------------------------------------------------------------->

    <!---------------------------------------INIT MAP--------------------------------------------->
    <script defer src="../ressources/js/map.js"></script>
    <script>
        window.onload = function(){
            console.log("loaded");
            let CommuneDepartJSON = <?= $CommuneDepart->toJson() ?>;
            let CommuneArriveeJSON = <?= $CommuneArrivee->toJson() ?>;
            initMap(CommuneDepartJSON, CommuneArriveeJSON);
            let tabTronconJSON = <?= json_encode($troncons) ?>;
            plotTroncon(tabTronconJSON);
            document.getElementById("loading").classList.add("hidden")
        }
    </script>
    <!-------------------------------------------------------------------------------------------->

<?php } ?>
<script src="../src/js/AutoCompletion.js" defer></script>