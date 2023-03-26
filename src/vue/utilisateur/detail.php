<?php
/** @var \App\PlusCourtChemin\Modele\DataObject\Utilisateur $utilisateur */

use App\PlusCourtChemin\Lib\ConnexionUtilisateur;

$login = $utilisateur->getLogin();
$loginHTML = htmlspecialchars($login);
$prenomHTML = htmlspecialchars($utilisateur->getPrenom());
$nomHTML = htmlspecialchars($utilisateur->getNom());
$loginURL = rawurlencode($login);
?>

<p>
    Utilisateur <?= "$prenomHTML $nomHTML" ?> de login <?= $loginHTML ?>

    <?php if (ConnexionUtilisateur::estUtilisateur($login) || ConnexionUtilisateur::estAdministrateur()) { ?>
        <a href="<?= $generateurUrl->generate('formulaireMiseAJour', ["idUtilisateur" => $loginURL]) ?>">(mettre
            à jour)</a>
        <a href="<?= $generateurUrl->generate('supprimerUtilisateur', ["idUtilisateur" => $loginURL]) ?>">(supprimer)</a>
    <?php } ?>
</p>
<p>
<h2>Mes trajets : </h2>
<ul>
    <?php
    foreach ($trajets as $trajet) {
        echo '<li> de ' . $trajet->getCommuneDepart()->getNomCommune() . ' vers ' .
            $trajet->getCommuneArrivee()->getNomCommune() . '</li>';
    }
    ?>
</ul>

</p>