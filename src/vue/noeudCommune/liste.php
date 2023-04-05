<?php

use App\PlusCourtChemin\Modele\DataObject\NoeudCommune;

$nextstart = $start + $limit;

$nextpass = $start + 1000;

$previousstart = max($start - $limit,0);

$previouspass = max($start - 1000,0);
echo <<<HTML
<a href="plusCourtChemin">Calculer un plus court chemin</a>

<h3>Liste des nœuds communes :</h3>

<div style ="margin:auto;width:fit-content;display: flex">
<form action = "./communesKeyWord" method = "post">
    <input type="submit" name="previous" value = "<<">
    <input type = "hidden" name="start" value=$previouspass>
</form>

<form action = "./communesKeyWord" method = "post">
    <input type="submit" name="previous" value = "-">
    <input type = "hidden" name="start" value=$previousstart>
</form>

<p style = "margin:auto;width:fit-content;">$start &#8594; $nextstart</p>

<form action = "./communesKeyWord" method = "post">
    <input type="submit"  name="next" value = "+">
    <input type = "hidden" name="start" value=$nextstart>
</form>

<form action = "./communesKeyWord" method = "post">
    <input type="submit"  name="next" value = ">>">
    <input type = "hidden" name="start" value=$nextpass>
</form>
</div>

<form action = "./communesKeyWord" method = "post">
    <input type="submit" id="search_keyword" name="search_keyword" value = "Rechercher">
    <input type = "text" id = "keyword" name ="keyword" required>
</form>

<ul id ="liste_Noeud_Commune">
HTML;
function my_sort(NoeudCommune $a, NoeudCommune $b)
{
    return str_replace("É", "E", $a->getNomCommune()) > str_replace("É", "E", $b->getNomCommune());
}

usort($noeudsCommunes, "my_sort");
foreach ($noeudsCommunes as $noeudCommune) {
    echo '<li class ="commune visible ' . str_replace("É", "E", $noeudCommune->getNomCommune())[0] . '" style = "position :relative; padding:5px;">';
    echo $noeudCommune->getNomCommune();
    echo " <a style ='position:absolute; right:0' href=\"detailCommune/" . $noeudCommune->getGid() . "\">(Détail)</a>";
    echo '</li>';
}
echo "</ul>\n";
?>
<script src="../src/js/NoeudCommune.js"></script>