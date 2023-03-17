let autoCompletionDepart = document.getElementById("autocompletionDepart");
autoCompletionDepart.style.borderWidth = "0px";
let autoCompletionArrivee = document.getElementById("autocompletionArrivee");
autoCompletionArrivee.style.borderWidth = "0px";
let villeDepart = document.getElementById("nomCommuneDepart_id");
let villeArrivee = document.getElementById("nomCommuneArrivee_id");

let indexDefilement = 0;

let autoCompletionTarget; // LA CHAMP D'AUTOCOMPLETION ACTUELLEMENT SELECTIONNEE (soit autoCompletionDepart ou autoCompletionArrivee)

let request;

let minuteur;

let villes;

let nomVilleDepart = null;

let nomVilleArrivee = null;


function afficheVilles(tableau) {
    videVilles();
    for (const ville of tableau) {
        const p = document.createElement("p");
        p.innerHTML = ville;
        p.id = ville;
        autoCompletionTarget.appendChild(p);
    }
    autoCompletionTarget.classList.remove("hidden");
}

function videVilles() {
    autoCompletionTarget.innerHTML = "";
    autoCompletionTarget.classList.add("hidden");
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
        let url = "chercherVille/" + encodeURIComponent(stringVille);
        request = new XMLHttpRequest();
        startLoadingAction();
        request.open("GET", url, true);
        request.addEventListener("load", function () {
            callback(request);
            endLoadingAction();
        });
        request.send(null);
    }
}


function callback_4(req) {
    let data = JSON.parse(req.responseText);
    villes = data;
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
        requeteAJAX(ville.value, callback_4, startLoadingAction, endLoadingAction)
    }, 200);
}

villeDepart.addEventListener('input', function () {
    RequeteVille(villeDepart);
});

villeArrivee.addEventListener('input', function () {
    RequeteVille(villeArrivee);
});

autoCompletionDepart.addEventListener('mousedown', function (event) {
    villeDepart.value = event.target.innerHTML;
    nomVilleDepart = villes.filter(function (ville) {
        return ville.nomCommune = event.target.innerHTML
    })
    miseAJourMap(nomVilleDepart, nomVilleArrivee)
    videVilles();
})

autoCompletionArrivee.addEventListener('mousedown', function (event) {
    villeArrivee.value = event.target.innerHTML;
    nomVilleArrivee = villes.filter(function (ville) {
        return ville.nomCommune = event.target.innerHTML
    })
    miseAJourMap(nomVilleDepart, nomVilleArrivee)
    videVilles();    
})

villeDepart.addEventListener("focusout", function (event) {
    videVilles();
    indexDefilement = 0;
    autoCompletionTarget = null;
})

villeArrivee.addEventListener("focusout", function (event) {
    videVilles();
    indexDefilement = 0;
    autoCompletionTarget = null;
})

villeDepart.addEventListener("focusin", function (event) {
    autoCompletionTarget = autoCompletionDepart;
    RequeteVille(villeDepart);
})

villeArrivee.addEventListener("focusin", function (event) {
    autoCompletionTarget = autoCompletionArrivee;
    RequeteVille(villeArrivee);
})

villeDepart.addEventListener("keydown", function (e) {
    flecheDefilement(e, villeDepart);
})

villeArrivee.addEventListener("keydown", function (e) {
    flecheDefilement(e, villeArrivee);
})

function flecheDefilement(e, ville) {
    let oldIndex = indexDefilement;
    let isArrow = true;
    if (e.key == "ArrowUp") {
        if (indexDefilement > 0) {
            indexDefilement--;
        }
    } else if (e.key == "ArrowDown") {
        if (indexDefilement + 1 < autoCompletionTarget.childElementCount && indexDefilement < 20) {
            indexDefilement++;
        }
    } else {
        isArrow = false;
    }
    if (e.key == "Enter") {     // on valide le choix
        let villeSelectionnee = autoCompletionTarget.childNodes.item(indexDefilement);
        ville.value = villeSelectionnee.innerHTML;      
        videVilles();       
        e.preventDefault()  // on annule le fait que le formulaire s'envoie alors qu'on souhaite simplement valider la ville
    }
    if (isArrow && oldIndex !== indexDefilement) {
        /*
            on gère la liste défilante, en mettant à jour le CSS et en ajustant la barre de défilement en fonction 
            de l'élément courant
        */

        let nomVille = autoCompletionTarget.childNodes.item(indexDefilement);
        nomVille.style.backgroundColor = "black";
        ville.value = nomVille.innerHTML;
        let oldVille = autoCompletionTarget.childNodes.item(oldIndex);
        oldVille.style.backgroundColor = "grey";
        let elementVille = document.getElementById(nomVille.innerHTML);

        elementVille.scrollIntoView({ behavior: 'smooth', block: 'center' })
    }

}
function miseAJourMap(villeDepart, villeArrivee) {
    let coorDepart = null;
    if (villeDepart !== null) {
        coorDepart = {};
        coorDepart['lat'] = villeDepart[0]['lat'];
        coorDepart['long'] = villeDepart[0]['long'];
    }
    let coorArrivee = null;
    if (villeArrivee !== null) {
        coorArrivee = {};
        coorArrivee['lat'] = villeArrivee[0]['lat'];
        coorArrivee['long'] = villeArrivee[0]['long'];
    }
    initMap(coorDepart, coorArrivee);
}

