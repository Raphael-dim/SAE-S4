<?php

echo <<<HTML
<a href="?action=plusCourtChemin&controleur=noeudCommune">Calculer un plus court chemin</a>

<h3>Liste des noeuds communes :</h3>
<ul>
HTML;
function my_sort(\App\PlusCourtChemin\Modele\DataObject\NoeudCommune $a,\App\PlusCourtChemin\Modele\DataObject\NoeudCommune $b)
{
    return $a->getNomCommune()>$b->getNomCommune();
}
usort($noeudsCommunes,"my_sort");
foreach ($noeudsCommunes as $noeudCommune) {
    echo '<li style = "position :relative; padding:5px;">';
    echo $noeudCommune->getNomCommune();
    echo " <a style ='position:absolute; right:0' href=\"?action=afficherDetail&controleur=noeudCommune&gid={$noeudCommune->getGid()}\">(Détail)</a>";
    echo '</li>';
}
echo "</ul>\n";