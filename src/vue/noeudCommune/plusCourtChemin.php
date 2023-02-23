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

    let escaleHTML =
        '<p class="InputAddOn"> ' +
            '<label class="InputAddOn-item" for="nomCommuneArrivee_id">Nom de la commune d\'escale ' + (document.querySelectorAll(".InputAddOn").length-1) + '</label> ' +
            '<input class="InputAddOn-field" type="text" value="" placeholder="Ex : Menton" name="nomCommuneArrivee" id="nomCommuneArrivee_id"> ' +
        '</p>'

    function test(){
        document.querySelectorAll(".InputAddOn")[document.querySelectorAll(".InputAddOn").length-1].insertAdjacentHTML('beforebegin',escaleHTML);
    }
</script>

<?php if (!empty($_POST)) { ?>
    <p>
        Le plus court chemin entre <?= $nomCommuneDepart ?> et <?= $nomCommuneArrivee ?> mesure <?= $distance ?>km.
    </p>
<?php } ?>