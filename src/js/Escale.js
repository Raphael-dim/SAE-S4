let nbEscale = 0;

function addEscale(){
    console.log("ok");
    nbEscale++;
    let escaleHTML =
        '<p class="InputAddOn"> ' +
        '<input placeholder="Nom de la commune d\'escale ' + nbEscale.toString() + '" class="InputAddOn-field nomCommune" type="text" value=""\n autocomplete="off" name="nomCommuneEscale' + nbEscale.toString() + '" id="nomCommuneEscale' + nbEscale.toString() + '_id" required>' +
    '</p>' +
    '<div class="autocompletion hidden" id="autocompletionArrivee"></div>';
    document.querySelector(".InputAddOn:last-of-type").insertAdjacentHTML('beforebegin',escaleHTML);
    init();
}