
<?php

$nextstart = $start+$limit;

$nextpass = $start+1000;

$previousstart = $start-$limit;

$previouspass = $start-1000;
echo <<<HTML
<a href="?action=plusCourtChemin&controleur=noeudCommune">Calculer un plus court chemin</a>

<h3>Liste des noeuds communes :</h3>

<div style ="margin:auto;width:fit-content;display: flex">
<form action = "controleurFrontal.php?action=afficherListe&controleur=noeudCommune" method = "post">
    <input type="submit" id="previous" name="previous" value = "<<">
    <input type = "hidden" name="start" value=$previouspass>
</form>

<form action = "controleurFrontal.php?action=afficherListe&controleur=noeudCommune" method = "post">
    <input type="submit" id="previous" name="previous" value = "-">
    <input type = "hidden" name="start" value=$previousstart>
</form>

<p style = "margin:auto;width:fit-content;">$start &#8594 $nextstart</p>

<form action = "controleurFrontal.php?action=afficherListe&controleur=noeudCommune" method = "post">
    <input type="submit" id="next" name="next" value = "+">
    <input type = "hidden" name="start" value=$nextstart>
</form>

<form action = "controleurFrontal.php?action=afficherListe&controleur=noeudCommune" method = "post">
    <input type="submit" id="next" name="next" value = ">>">
    <input type = "hidden" name="start" value=$nextpass>
</form>
</div>

<form action = "controleurFrontal.php?action=afficherListe&controleur=noeudCommune" method = "post">
    <input type="submit" id="search_keyword" name="search_keyword" value = "search">
    <input type = "text" id = "keyword" name ="keyword" required>
</form>

<ul id ="liste_Noeud_Commune">
HTML;
function my_sort(\App\PlusCourtChemin\Modele\DataObject\NoeudCommune $a,\App\PlusCourtChemin\Modele\DataObject\NoeudCommune $b)
{
        return str_replace("É","E",$a->getNomCommune())>str_replace("É","E",$b->getNomCommune());
}
usort($noeudsCommunes,"my_sort");
foreach ($noeudsCommunes as $noeudCommune) {
    echo '<li class ="commune visible '. str_replace("É","E",$noeudCommune->getNomCommune())[0].'" style = "position :relative; padding:5px;">';
    echo $noeudCommune->getNomCommune();
    echo " <a style ='position:absolute; right:0' href=\"?action=afficherDetail&controleur=noeudCommune&gid={$noeudCommune->getGid()}\">(Détail)</a>";
    echo '</li>';
}
echo "</ul>\n";
?>
    <script src="../ressources/js/NoeudCommune.js"></script>