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
        <h2 > Mes trajets : </h2>(sans doublons)
        <ul > ';

        // La liste des trajets uniques
        $trajetsUniques = array();

        // On parcourt tous les trajets du tableau
        foreach ($trajets as $trajet) {
            // On regarde si le trajet est déjà présent dans la liste des trajets uniques
            $estDejaPresent = false;
            foreach ($trajetsUniques as $trajetUnique) {
                if ($trajet->getCommuneDepart()->getNomCommune() == $trajetUnique->getCommuneDepart()->getNomCommune() &&
                    $trajet->getCommuneArrivee()->getNomCommune() == $trajetUnique->getCommuneArrivee()->getNomCommune()) {
                    $estDejaPresent = true;
                    break;
                }
            }
            // Si le trajet n'est pas déjà présent dans la liste des trajets uniques, on l'ajoute
            if (!$estDejaPresent) {
                $trajetsUniques[] = $trajet;
            }
        }
        foreach ($trajetsUniques as $trajet) {
            echo '<li><form method="post" action="../calculer">
                <input type="submit" value="de ' . $trajet->getCommuneDepart()->getNomCommune() .
                 ' vers ' . $trajet->getCommuneArrivee()->getNomCommune() . '">
                    <input type="hidden" name="nomsCommune[]" value="' . $trajet->getCommuneDepart()->getNomCommune() . '">
                    <input type="hidden" name="nomsCommune[]" value="' . $trajet->getCommuneArrivee()->getNomCommune() . '">
                    
                    </form></li>';
        }
        echo '</ul></div>';
    }
    ?>



