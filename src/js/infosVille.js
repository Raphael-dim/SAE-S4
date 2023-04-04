let divInfoVille = document.getElementById("infoVille");
let imageFleche = document.getElementsByClassName("fleche")[0];
let infoAffiches = false;
let accolade = document.getElementsByClassName("accolade")[0];

cacherInfo();

accolade.addEventListener('click', switchAffichageInfo);
imageFleche.addEventListener('click', switchAffichageInfo);

function switchAffichageInfo() {
    if (infoAffiches) {
        cacherInfo();
    } else {
        afficherInfo()
    }
}

function afficherInfo() {
    divInfoVille.style.animation = "divInfoIn 3.5s";
    divInfoVille.style.display = "block";
    accolade.style.display = "none";
    imageFleche.style.left = "300px";
    imageFleche.style.animation = "divInfoIn 0.5s";
    imageFleche.style.transform = "rotate(180deg)";
    infoAffiches = true;
    if (divInfoVille.childNodes.length == 0) {
        let msg = document.createElement("p");
        msg.innerHTML = "Si vous cliquez sur la carte, des informations sur la ville la plus proche seront affichées ici. " +
            "<br> En cachant ce panneau, vous désactivez cette fonctionnalité et économisez ainsi des ressources.<br> " +
            "<br> Pour limiter l'impact de cette page, veuillez fermer ce panneau lorsqu'il ne vous sert pas."
        divInfoVille.appendChild(msg)
    }
}

function cacherInfo() {
    // divInfoVille.style.animation = "divInfoOut 3.5s"
    accolade.style.display = "block"
    divInfoVille.style.display = "none"
    imageFleche.style.left = "9px";
    imageFleche.style.transform = "";
    infoAffiches = false;
}

function afficherDetail(infos, latitude, longitude) {
    if (infoAffiches) {
        divInfoVille.innerHTML = "";

        let infoBD = document.createElement("p");
        infoBD.innerHTML = infos.map(element => element["nom_comm"]) + "<br>" +
            infos.map(element => element["statut"])
            + "<br>Latitude : " + latitude + " <br> Longitude : " + longitude;
        divInfoVille.appendChild(infoBD);

        info(infos.map(element => element["nom_comm"]));

        let link = document.createElement("a");
        link.innerHTML = "Wikipedia"
        link.href = "https://wikipedia.com";
        divInfoVille.appendChild(link)
    }
}

function info(ville) {
    let urlDesPages = "https://fr.wikipedia.org/w/api.php?action=query&list=search&prop=info&inprop=url&utf8=&" +
    "format=json&origin=*&srlimit=20&lang=fr&srsearch=" + ville;
    let requete = new XMLHttpRequest();
    requete.open("GET", urlDesPages, true);
    requete.addEventListener("load", function () {
        let data = JSON.parse(requete.responseText);
        let idPage = data.query.search[0]['pageid'];
        let urlLaPage = "https://fr.wikipedia.org/w/api.php?origin=*&action=query&pageids=" + idPage + "&" +
            "prop=extracts&exintro&explaintext&format=json";
        let requete2 = new XMLHttpRequest();
        requete2.open("GET", urlLaPage, true);
        requete2.addEventListener("load", function () {
            let data2 = JSON.parse(requete2.responseText);
            let lesDonnees = data2.query.pages[idPage].extract;
            let p = document.createElement("p");
            p.innerHTML = lesDonnees;
            divInfoVille.appendChild(p)
        });
        requete2.send(null)
    });
    requete.send(null);
}