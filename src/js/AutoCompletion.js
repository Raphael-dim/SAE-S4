let autoCompletionDepart = document.getElementById("autocompletionDepart");
autoCompletionDepart.style.borderWidth = "0px";
let autoCompletionArrivee = document.getElementById("autocompletionArrivee");
autoCompletionArrivee.style.borderWidth = "0px";
let villeDepart = document.getElementById("nomCommuneDepart_id");
let villeArrivee = document.getElementById("nomCommuneArrivee_id");

let indexDefilement = 0;

let autoCompletionTarget; // LA CHAMP D'AUTOCOMPLETION ACTUELLEMENT SELECTIONNEE (soit autoCompletionDepart ou autoCompletionArrivee)

let request;

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
    let url = "../web/villes?ville=" + encodeURIComponent(stringVille);
    request = new XMLHttpRequest();
    startLoadingAction();
    request.open("GET", url, true);
    request.addEventListener("load", function () {
        callback(request);
        endLoadingAction();
    });
    request.send(null);
}

function callback_4(req) {
    let data = JSON.parse(req.responseText);
    let names = data.map(element => element["nomCommune"]);
    afficheVilles(names);
}

function maRequeteAJAX(chaine) {
    requeteAJAX(chaine, callback_4, startLoadingAction, endLoadingAction);

}

function RequeteVille(Ville) {
    if (request !== undefined) {
        request.abort();
    }
    if (Ville.value.length == 0) {
        videVilles();
    } else {
        autoCompletionTarget = autoCompletionDepart;
        maRequeteAJAX(Ville.value);
    }
}

villeDepart.addEventListener('input', function () {
    RequeteVille(villeDepart);
});

villeArrivee.addEventListener('input', function () {
    RequeteVille(villeArrivee);
});

autoCompletionDepart.addEventListener('mousedown', function (event) {
    villeDepart.value = event.target.innerHTML;
    autoCompletionDepart.innerHTML = "";
})

autoCompletionArrivee.addEventListener('mousedown', function (event) {
    villeArrivee.value = event.target.innerHTML;
    autoCompletionArrivee.innerHTML = "";
})

villeDepart.addEventListener("focusout", function (event) {
    videVilles();
    indexDefilement = 0;
})

villeArrivee.addEventListener("focusout", function (event) {
    videVilles();
    indexDefilement = 0;
})

villeDepart.addEventListener("focusin", function (event) {
    RequeteVille(villeDepart);
})

villeArrivee.addEventListener("focusin", function (event) {
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
        if (indexDefilement < 20) {
            indexDefilement++;
        }
    } else {
        isArrow = false;
    }
    if (isArrow) {
        let nomVille = autoCompletionDepart.childNodes.item(indexDefilement);
        nomVille.style.backgroundColor = "black";
        ville.value = nomVille.innerHTML;
        let oldVille = autoCompletionDepart.childNodes.item(oldIndex);
        oldVille.style.backgroundColor = "grey";
        let elementVille = document.getElementById(nomVille.innerHTML);

        elementVille.scrollIntoView({behavior: 'smooth', block: 'center'})


    }
}

