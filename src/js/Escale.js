let nbEscale = 0;
let noms = JSON.parse(document.currentScript.dataset.communes);
for(let i = 0;i<noms.length-2;i++){
    addEscale();
}
console.log(m);

function addEscale(){
    nbEscale++;
    let escaleHTML =
        '<p class="InputAddOn"> ' +
        '<input placeholder="Nom de la commune d\'escale ' + nbEscale.toString() + '" class="InputAddOn-field nomCommune" type="text" value="' + noms[nbEscale] + '"\n autocomplete="off" name="nomsCommune[]" required>' +
    '</p>' +
    '<div class="autocompletion hidden" id="autocompletionArrivee"></div>';
    document.getElementsByClassName("InputAddOn")[nbEscale].insertAdjacentHTML('beforebegin',escaleHTML);
    init();
}