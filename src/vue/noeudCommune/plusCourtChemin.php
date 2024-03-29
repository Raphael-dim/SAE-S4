<form class="saisieVille" action="calculer" method="post">
    <div id = "inputs">
        <p class="InputAddOn">
            <input placeholder="Nom de la commune de départ" class="InputAddOn-field nomCommune" type="text" value="<?php echo (!empty($_POST))? $Communes[0]->getNomCommune():''?>"
                   autocomplete="off" name="nomsCommune[]" required>
            <!-- <img id="loading" src="img/loading.gif"> -->
            <!--            <img class="localiser" src ="assets/img/marker.png">-->
        </p>
        <p class="InputAddOn">
            <input placeholder="Nom de la commune d'arrivée" class="InputAddOn-field nomCommune" type="text" value="<?php echo (!empty($_POST))? end($Communes)->getNomCommune():''?>"
                   autocomplete="off" name="nomsCommune[]" required>
        </p>
    </div>
    <!-- <img id="loading" src="img/loading.gif"> -->
    <div class="autocompletion hidden" id="autocompletion"></div>
    <div>
        <input type="hidden" name="XDEBUG_TRIGGER">
        <input class="InputAddOn-field" type="button" value="Ajouter une escale" onclick="addEscale()"/>
        <input class="InputAddOn-field" type = "button" value="Localiser" id="localiser">
        <input class="InputAddOn-field" type="submit" value="Calculer"/>
    </div>
    <?php if (!empty($_POST)) { ?>

        <p>
            Le plus court chemin entre : <br>
            <?php
            echo $Communes[0]->getNomCommune();
            for($i = 1;$i<=count($Communes)-1;$i++){
                if($i == count($Communes)-1){
                    echo ' et ' . $Communes[$i]->getNomCommune();
                }else{
                    echo ', ' . $Communes[$i]->getNomCommune() ;
                }
            }
            ?><br>
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
<div class="infoVille" id="infoVille"></div>
<img class="fleche" src="<?= $assistantUrl->getAbsoluteUrl("assets/img/fleche.png") ?>" alt="fleche">
<div class="accolade"></div>


<!-------------------------------------GOOGLE MAPS API---------------------------------------->
<script defer src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<div id="map" class="map"></div>


<script defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCNgiSeE--QYZtlP4qYMTDatGQrDXgql8M&v=weekly"></script>

<!---------------------------------------INIT MAP--------------------------------------------->
<script defer src="../src/js/map.js"></script>
<?php if (!empty($_POST)) {?>

    <script>
        window.onload = function () {
            let CommunesJSON = <?= json_encode($Communes) ?>;
            initMap(CommunesJSON);
            let tabTronconJSON = <?= json_encode($troncons) ?>;
            plotTroncon(tabTronconJSON);
            document.getElementById("loading").classList.add("hidden")
        }
    </script>
    <!-------------------------------------------------------------------------------------------->

<?php } ?>
<script src="../src/js/infosVille.js" defer></script>
<script src="../src/js/AutoCompletion.js" data-nomcommunes='<?php echo (!empty($_POST))? json_encode($_POST['nomsCommune']):'[]' ?>' data-communes='<?php echo (!empty($_POST))? json_encode(array_map(function($n) { return ['lat' => floatval($n->getLongNoeud()),'long' => floatval($n->getLatNoeud())];}, $noeuds)):'[]' ?>' defer></script>