let autoCompletionDepart = document.getElementById("autocompletionDepart");
autoCompletionDepart.style.borderWidth = "0px";
let autoCompletionArrivee = document.getElementById("autocompletionArrivee");
autoCompletionArrivee.style.borderWidth = "0px";

let autoCompletionTarget;

function afficheVilles(tableau) {
    videVilles();
    for (const ville of tableau) {
        const p = document.createElement("p");
        p.innerHTML = ville;
        autoCompletionTarget.appendChild(p);
    }
}

function videVilles() {
    autoCompletionTarget.innerHTML = "";
}

function startLoadingAction() {
    // document.getElementById("loading").style.visibility = "visible";
}

function endLoadingAction() {
    // document.getElementById("loading").style.visibility = "hidden";
}

function requeteAJAX(stringVille, callback, startLoadingAction, endLoadingAction) {
    let url = "../web/controleurFrontal.php?controleur=RequeteVilleController&action=getVille&ville=" + encodeURIComponent(stringVille);
    let requete = new XMLHttpRequest();
    startLoadingAction();
    requete.open("GET", url, true);
    requete.addEventListener("load", function () {
        callback(requete);
        endLoadingAction();
    });
    requete.send(null);
}

function callback_4(req) {
    let data = JSON.parse(req.responseText);
    let names = data.map(element => element["nomCommune"]);
    afficheVilles(names);
}

function maRequeteAJAX(chaine) {
    requeteAJAX(chaine, callback_4, startLoadingAction, endLoadingAction);
}

let villeDepart = document.getElementById("nomCommuneDepart_id");
villeDepart.addEventListener('input', function () {
    if (villeDepart.value.length >= 2) {
        autoCompletionTarget = autoCompletionDepart;
        maRequeteAJAX(villeDepart.value);
    }
});

let villeArrivee = document.getElementById("nomCommuneArrivee_id");
villeArrivee.addEventListener('input', function () {
    if (villeArrivee.value.length >= 2) {
        autoCompletionTarget = autoCompletionArrivee;
        maRequeteAJAX(villeArrivee.value);
    }
});

autoCompletionDepart.addEventListener('click', function (event) {
    villeDepart.value = event.target.innerHTML;
    autoCompletionDepart.innerHTML = "";
})

autoCompletionArrivee.addEventListener('click', function (event) {
    villeArrivee.value = event.target.innerHTML;
    autoCompletionArrivee.innerHTML = "";
})
