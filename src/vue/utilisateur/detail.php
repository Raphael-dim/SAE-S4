<?php
/** @var \App\PlusCourtChemin\Modele\DataObject\Utilisateur $utilisateur */

use App\PlusCourtChemin\Lib\ConnexionUtilisateur;

$login = $utilisateur->getLogin();
$loginHTML = htmlspecialchars($login);
$prenomHTML = htmlspecialchars($utilisateur->getPrenom());
$nomHTML = htmlspecialchars($utilisateur->getNom());
$loginURL = rawurlencode($login);
?>

<div class="barre_utilisateur">
    <img id="picture" src="../../web/assets/img/profile_pic.jpg" alt="profile">
    <div id="infos_utilisateur">
        <h1 id="nom"> <?= $prenomHTML ?> <?= $nomHTML ?></h1>
        <h2 id="identifiant"> <?= $loginHTML ?></h2>
    </div>
    <?php
    $bool = false;
    if (ConnexionUtilisateur::getLoginUtilisateurConnecte() == $login || ConnexionUtilisateur::estAdministrateur()) {
        $bool = true;
        echo '<a href="../deconnecter">
        <img class="sortie" src="../../web/assets/img/logo_sortie.png" alt="Déconnexion"></a>';
    }

    echo '</div><div id="modif" style="margin-bottom: 60px">';
    if ($bool) {
        echo '<a class="lien"
       href="' . $generateurUrl->generate("formulaireMiseAJour", ["idUtilisateur" => $loginURL]) . '">
        Modifier les informations</a>';
    }
    echo '</div><p>';
    if (ConnexionUtilisateur::estUtilisateur($login) || ConnexionUtilisateur::estAdministrateur()) {
        echo '<a class="lien" href="' . $generateurUrl->generate("supprimerUtilisateur", ["idUtilisateur" => $loginURL]) . '">supprimer</a>';
    }
    if (ConnexionUtilisateur::estUtilisateur($login)) {
        echo '<div class="trajets">
        <h2 > Mes trajets : </h2 >
        <ul > ';
        foreach ($trajets as $trajet) {
            echo ' < li ><a href = "" > de ' . $trajet->getCommuneDepart()->getNomCommune() . ' vers ' .
                $trajet->getCommuneArrivee()->getNomCommune() . ' </a ></li > ';
        }
        echo '
        </ul >
    </div >
    ';
    }
    ?>



