let villesNodes = [...document.getElementsByClassName('nomCommune')];
let currentVilleNode = villesNodes[0];

let indexDefilement = 0;

let autoCompletion = document.getElementById("autocompletion");

let request;

let communes = JSON.parse(document.currentScript.dataset.communes);

let minuteur;

let villesSuggests;
let currentVilles = new Array();


inputs =document.getElementsByClassName("nomCommune");
let currentTarget = inputs[0];

Array.prototype.forEach.call(inputs,function(input){
    input.addEventListener("focusin",function(e){
        currentTarget.style.borderColor = "black"
        e.target.style.borderColor = "cornflowerblue";
        currentTarget = e.target;
    })
});

init();

imageLocaliser = document.getElementById("localiser");

imageLocaliser.addEventListener("mousedown", function (event) {
    navigator.geolocation.getCurrentPosition(localiser);
})

function localiser(pos) {
    let latitude = pos.coords.latitude;
    let longitude = pos.coords.longitude;
    let Latlng = [{ "lat": latitude, "long": longitude}];
    currentVilles.splice(0, 1, Latlng);
    requete(latitude, longitude);
}

/*function requete(latitude, longitude) {
    currentVilles.splice(0, 1, [{ "lat": latitude, "long": longitude}]);
    console.log(currentVilles);
    let url = "chercherVilleCoor/" + encodeURIComponent(latitude) + "/" + encodeURIComponent(longitude);
    let request = new XMLHttpRequest();
    request.open("GET", url, true);
    request.addEventListener("load", function () {
        let data = JSON.parse(request.responseText);
        let ville = data[0];
        villesNodes[0].value = ville["nom_comm"];
        miseAJourMap(currentVilles);
    });
    request.send(null);
}*/

map.addListener('click', function(e) {
    requete(e.latLng.lat(), e.latLng.lng(), true);
});

function requete(latitude, longitude, afficherDetailVille = false) {
    currentVilles.splice(0, 1, [{ "lat": latitude, "long": longitude}]);
    let url = "chercherVilleCoor/" + encodeURIComponent(latitude) + "/" + encodeURIComponent(longitude);
    let request = new XMLHttpRequest();
    let data;
    request.open("GET", url, true);
    request.addEventListener("load", function () {
        data = JSON.parse(request.responseText);
        let name = data.map(element => element["nom_comm"]);
        currentTarget.value = (name[0]);
        if (afficherDetailVille) {
            afficherDetail(data)
        }
        miseAJourMap(currentVilles);
    });
    request.send(null);
}


/**
 * Initialise l'écoute de chaque ville.
 */
function init(){
    //Récupère toutes les villes
    villesNodes = [...document.getElementsByClassName('nomCommune')];

    villesNodes.forEach((villeNode) => {
        //Quand le contenu change
        villeNode.addEventListener('input', function (event) {
            RequeteVille(event.target);
        });

        //Quand la ville n'est plus selectionné
        villeNode.addEventListener("focusout", function () {
            videVilles();
            indexDefilement = 0;
        });

        //Quand la ville est selectionné
        villeNode.addEventListener("focusin", function (event) {
            currentVilleNode = event.target;
            villeNode.insertAdjacentElement('afterend',autoCompletion);
            RequeteVille(currentVilleNode);
        });

        //Quand une touche du clavier est appuyé
        villeNode.addEventListener("keydown", function (e) {
            flecheDefilement(e, currentVilleNode);
        });

        for(let i = currentVilles.length; i<villesNodes.length;i++){
            currentVilles.splice(currentVilles.length-1, 0,[communes[i]]);
        }
    });
}

/**
 * Affiche les villes dans la barre d'auto complétion
 *
 * @param tableau
 */
function afficheVilles(tableau) {
    videVilles();

    for (const ville of tableau) {
        const p = document.createElement("p");
        p.innerHTML = ville;
        p.id = ville;
        autoCompletion.appendChild(p);
    }
    autoCompletion.classList.remove("hidden");
}

function videVilles() {
    autoCompletion.innerHTML = "";
    autoCompletion.classList.add("hidden");
    indexDefilement = 0;
}

function startLoadingAction() {
    // document.getElementById("loading").style.visibility = "visible";
}

function endLoadingAction() {
    // document.getElementById("loading").style.visibility = "hidden";
}

function requeteAJAX(stringVille, callback, startLoadingAction, endLoadingAction) {
    /*
        On utilise les routes établies dans Routeur.php
    */
    if (stringVille != "") {
        let url = "chercherVille/" + encodeURI(stringVille);
        request = new XMLHttpRequest();
        startLoadingAction();
        request.open("GET", decodeURI(url), true);
        request.addEventListener("load", function () {
            callback(request);
            endLoadingAction();
        });
        request.send();
    }
}

function callback(req) {
    let data = JSON.parse(req.responseText);
    villesSuggests = data;
    let names = data.map(element => element["nomCommune"]);
    afficheVilles(names);
}

function RequeteVille(ville) {

    if (typeof minuteur == "number") {
        clearTimeout(minuteur);
    }
    if (request != null && request.readyState != 4) {
        request.abort()
    }

    minuteur = setTimeout(() => {
        requeteAJAX(ville.value, callback, startLoadingAction, endLoadingAction)
    }, 200);
}

autoCompletion.addEventListener('mousedown', function (event) {
    currentVilleNode.value = event.target.innerHTML;
    currentVilles.splice(villesNodes.indexOf(currentVilleNode), 1, villesSuggests.filter(function (v) {
        return v.nomCommune === event.target.innerHTML
    }));
    miseAJourMap(currentVilles)
    videVilles();
})

function flecheDefilement(e, ville) {
    let oldIndex = indexDefilement;
    let isArrow = true;
    if (e.key == "ArrowUp") {
        if (indexDefilement > 0) {
            indexDefilement--;
        }
    } else if (e.key == "ArrowDown") {
        if (indexDefilement + 1 < autoCompletion.childElementCount && indexDefilement < 20) {
            indexDefilement++;
        }
    } else {
        isArrow = false;
    }
    if (e.key == "Enter") {     // on valide le choix
        let villeSelectionnee = autoCompletion.childNodes.item(indexDefilement);
        e.preventDefault()  // on annule le fait que le formulaire s'envoie alors qu'on souhaite simplement valider la ville
        ville.value = villeSelectionnee.innerHTML;
        currentVilles.splice(villesNodes.indexOf(currentVilleNode), 1, villesSuggests.filter(function (v) {
            return v.nomCommune === villeSelectionnee.innerHTML;
        }));
        miseAJourMap(currentVilles)
        videVilles();
    }
    if (isArrow && oldIndex !== indexDefilement) {
        /*
            on gère la liste défilante, en mettant à jour le CSS et en ajustant la barre de défilement en fonction
            de l'élément courant
        */

        let nomVille = autoCompletion.childNodes.item(indexDefilement);
        nomVille.style.backgroundColor = "black";
        ville.value = nomVille.innerHTML;
        let oldVille = autoCompletion.childNodes.item(oldIndex);
        oldVille.style.backgroundColor = "grey";
        let elementVille = document.getElementById(nomVille.innerHTML);

        elementVille.scrollIntoView({ behavior: 'smooth', block: 'center' })
    }

}

function miseAJourMap(villes) {
    initMap(villes.map(v => (v[0] == undefined? 0:{lat: v[0]['lat'], long: v[0]['long']})));
}



/***** ESCALES ******/
let nbEscale = 0;
let noms = JSON.parse(document.currentScript.dataset.communes);

for(let i = 0;i<noms.length-2;i++){
    addEscale();
}

function addEscale(){
    nbEscale++;
    let escaleHTML =
        '<p class="InputAddOn"> ' +
        '<input placeholder="Nom de la commune d\'escale ' + nbEscale.toString() + '" class="InputAddOn-field nomCommune" type="text" value="' + ((typeof noms[nbEscale+1] != "undefined")? noms[nbEscale]:"") + '"\n autocomplete="off" name="nomsCommune[]" required>' +
        '</p>'
    document.getElementsByClassName("InputAddOn")[nbEscale].insertAdjacentHTML('beforebegin',escaleHTML);
    document.getElementsByClassName("InputAddOn")[nbEscale].addEventListener("focusin",function(e){
        currentTarget.style.borderColor = "black"
        e.target.style.borderColor = "cornflowerblue";
        currentTarget = e.target;
    })
    init();
}