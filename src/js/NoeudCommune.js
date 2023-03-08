function genererBoutons() {
    let main = document.getElementById("liste_Noeud_Commune");

    for (let i = 64 + 26; i > 64; i--) {
        let bt = document.createElement("button")
        bt.innerHTML = String.fromCharCode(i);
        bt.classList.add("unselected");
        bt.style.border = "solid black 1px";
        bt.addEventListener('click', bt_click, false)
        main.insertBefore(bt, main.firstChild);
    }
}

function bt_click(evt) {
    arg = evt.currentTarget.innerHTML;

    let select = document.getElementsByClassName(arg);
    let unselect = document.getElementsByClassName("commune");

    let currentselect = document.getElementsByClassName("selected")[0];

    if (currentselect != null) {
        currentselect.classList.remove("selected");
    }

    if (currentselect != null && currentselect.innerHTML === arg) {
        Array.from(unselect).forEach(function (un) {
            un.classList.remove("hidden");
            un.classList.add("visible");
        })
    } else {
        evt.currentTarget.classList.add("selected");
        Array.from(unselect).forEach(function (un) {
            un.classList.remove("visible");
            un.classList.add("hidden");
        })

        Array.from(select).forEach(function (sel) {
            sel.classList.remove("hidden");
            sel.classList.add("visible");
        })
    }


}

genererBoutons();