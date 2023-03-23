<form class="saisieVille" action="calculer" method="post">
        <p class="InputAddOn">
            <input placeholder="Nom de la commune de départ" class="InputAddOn-field nomCommune" type="text" value=""
                   autocomplete="off" name="nomCommuneDepart" required>
            <!-- <img id="loading" src="img/loading.gif"> -->
            <img class="localiser" src="assets/img/placeholder.png">
        </p>
        <p class="InputAddOn">
            <input placeholder="Nom de la commune d'arrivée" class="InputAddOn-field nomCommune" type="text" value=""
                   autocomplete="off" name="nomCommuneArrivee" required>
        </p>
        <!-- <img id="loading" src="img/loading.gif"> -->
        <div class="autocompletion hidden" id="autocompletion"></div>
    <div>
        <input type="hidden" name="XDEBUG_TRIGGER">
        <input class="InputAddOn-field" type="button" value="Ajouter une escale" onclick="addEscale()"/>
        <p>
            <input class="InputAddOn-field" type="submit" value="Calculer"/>
        </p>
    </div>

    <?php if (!empty($_POST)) { ?>
        <p>
            Le plus court chemin entre <?= $CommuneDepart->getNomCommune() ?> et <?= $CommuneArrivee->getNomCommune() ?>
            mesure <?= $distance ?>km.
        </p>
        <p>
            Temps d'execution : <?= $temps ?>s
        </p>
        <p id="loading">
            loading
        </p>
    <?php } ?>
</form>


<!-------------------------------------GOOGLE MAPS API---------------------------------------->
<script defer src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<div id="map" class="map"></div>


<script defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCNgiSeE--QYZtlP4qYMTDatGQrDXgql8M&v=weekly"></script>
<!-------------------------------------------------------------------------------------------->

<!---------------------------------------INIT MAP--------------------------------------------->
<script defer src="../src/js/map.js"></script>
<?php if (!empty($_POST)) { ?>

    <script>
        window.onload = function () {
            let CommuneDepartJSON = <?= $CommuneDepart->toJson() ?>;
            console.log("maj");
            let CommuneArriveeJSON = <?= $CommuneArrivee->toJson() ?>;
            initMap(CommuneDepartJSON, CommuneArriveeJSON);
            let tabTronconJSON = <?= json_encode($troncons) ?>;
            plotTroncon(tabTronconJSON);
            document.getElementById("loading").classList.add("hidden")
        }
    </script>
    <!-------------------------------------------------------------------------------------------->

<?php } ?>
<script src="../src/js/AutoCompletion.js"></script>
<script src="../src/js/Escale.js"></script>