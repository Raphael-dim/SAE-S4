let autoCompletion = document.getElementById("autocompletion");
autoCompletion.style.borderWidth = "0px";

function afficheVilles(tableau) {
    videVilles();
    for (const ville of tableau) {
        const p = document.createElement("p");
        p.innerHTML = ville;
        autoCompletion.appendChild(p);
    }
}

function videVilles() {
    autoCompletion.innerHTML = "";
}

function startLoadingAction() {
    // document.getElementById("loading").style.visibility = "visible";
}

function endLoadingAction() {
    // document.getElementById("loading").style.visibility = "hidden";
}

function requeteAJAX(stringVille, callback, startLoadingAction, endLoadingAction) {
    let url = "../web/controleurFrontal.php?controleur=RequeteVilleController&action=getVille&ville=" + encodeURIComponent(stringVille);
    console.log(url);
    let requete = new XMLHttpRequest();
    startLoadingAction();
    requete.open("GET", url, true);
    console.log(requete);
    requete.addEventListener("load", function () {
        callback(requete);
        endLoadingAction();
    });
    requete.send(null);
}

function callback_4(req) {
    console.log(JSON.parse(req.responseText));
    let data = JSON.parse(req.responseText);
    let names = data.map(element => element["nomCommune"]);
    afficheVilles(names);
}

function maRequeteAJAX(chaine) {
    requeteAJAX(chaine, callback_4, startLoadingAction, endLoadingAction);
}

let ville = document.getElementById("nomCommuneDepart_id");
ville.addEventListener('input', function () {
    if (ville.value.length >= 2) {
        maRequeteAJAX(ville.value);
    }
});

autoCompletion.addEventListener('click', function (event) {
    ville.value = event.target.innerHTML;
    autoCompletion.innerHTML = "";
})
