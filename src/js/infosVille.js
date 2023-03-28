let div = document.getElementById("infoVille")
div.style.borderWidth = "0px";

function afficherDetail(infos) {
    div.innerHTML = "";
    let p = document.createElement("a");
    p.innerHTML ="Wikipedia"
    p.href = "https://wikipedia.com";
    div.appendChild(p)
    info(infos.map(element => element["nom_comm"]));
}

function info(ville) {
    let urlDesPages = "https://en.wikipedia.org/w/api.php?action=query&list=search&prop=info&inprop=url&utf8=&" +
        "format=json&origin=*&srlimit=20&lang=fr&srsearch=" +ville;
    let requete = new XMLHttpRequest();
    requete.open("GET", urlDesPages, true);
    requete.addEventListener("load", function () {
        let data = JSON.parse(requete.responseText);
        let idPage = data.query.search[0]['pageid'];
        let urlLaPage = "https://en.wikipedia.org/w/api.php?origin=*&action=query&pageids=" + idPage + "&" +
            "prop=extracts&exintro&explaintext&format=json";
        let requete2 = new XMLHttpRequest();
        requete2.open("GET", urlLaPage, true);
        requete2.addEventListener("load", function () {
            let data2 = JSON.parse(requete2.responseText);
            let lesDonnees = data2.query.pages[idPage].extract;
            let p = document.createElement("p");
            p.innerHTML = lesDonnees;
            div.appendChild(p)
        });
        requete2.send(null)
    });
    requete.send(null);
}