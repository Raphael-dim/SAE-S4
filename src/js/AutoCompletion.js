let autoCompletionDepart = document.getElementById("autocompletionDepart");
autoCompletionDepart.style.borderWidth = "0px";
let autoCompletionArrivee = document.getElementById("autocompletionArrivee");
autoCompletionArrivee.style.borderWidth = "0px";
let villeDepart = document.getElementById("nomCommuneDepart_id");
let villeArrivee = document.getElementById("nomCommuneArrivee_id");


let autoCompletionTarget; // LA CHAMP D'AUTOCOMPLETION ACTUELLEMENT SELECTIONNEE (soit autoCompletionDepart ou autoCompletionArrivee)

let request;

function afficheVilles(tableau) {
    videVilles();
    for (const ville of tableau) {
        const p = document.createElement("p");
        p.innerHTML = ville;
        autoCompletionTarget.appendChild(p);
    }
    autoCompletionTarget.classList.remove("hidden");
}

function videVilles() {
    autoCompletionTarget.innerHTML = "";
    autoCompletionTarget.classList.add("hidden");
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


function RequeteVille(Ville){
    if (request !== undefined){
        request.abort();
    }
    if (Ville.value.length==0){
        videVilles();
    }
    else{
        autoCompletionTarget = autoCompletionDepart;
        maRequeteAJAX(Ville.value);
    }
}

villeDepart.addEventListener('input', function () {
    RequeteVille(villeDepart);
});

villeArrivee.addEventListener('input', function () {
    if (request !== undefined){
        request.abort();
    }
    if (villeArrivee.value.length==0){
        videVilles();
    }else{
        autoCompletionTarget = autoCompletionArrivee;
        maRequeteAJAX(villeArrivee.value);
    }
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
})

villeArrivee.addEventListener("focusout", function (event) {
        videVilles();
})


villeDepart.addEventListener("focusin", function (event) {
    RequeteVille(villeDepart);
})

villeArrivee.addEventListener("focusin", function (event) {
    RequeteVille(villeArrivee);
})


// villeArrivee.addEventListener("focusout", function (event) {

// })