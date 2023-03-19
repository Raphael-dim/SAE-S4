<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Configuration\Configuration;
use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Lib\MessageFlash;
use App\PlusCourtChemin\Lib\MotDePasse;
use App\PlusCourtChemin\Lib\VerificationEmail;
use App\PlusCourtChemin\Modele\DataObject\Utilisateur;
use App\PlusCourtChemin\Modele\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ControleurUtilisateur extends ControleurGenerique
{

    public static function afficherListe(): Response
    {
        $utilisateurs = (new UtilisateurRepository())->recuperer();     //appel au modèle pour gerer la BD
        return ControleurUtilisateur::afficherVue('vueGenerale.php', [
            "utilisateurs" => $utilisateurs,
            "pagetitle" => "Liste des utilisateurs",
            "cheminVueBody" => "utilisateur/liste.php"
        ]);
    }

    public static function afficherDetail($idUtilisateur): Response
    {
        $utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);
        if ($utilisateur === null) {
            MessageFlash::ajouter("warning", "Login inconnu.");
            return ControleurUtilisateur::rediriger("utilisateur", "afficherListe");
        } else {
            $trajets = $utilisateur->getTrajets();

            return ControleurUtilisateur::afficherVue('vueGenerale.php', [
                "utilisateur" => $utilisateur,
                "trajets" => $trajets,
                "pagetitle" => "Détail de l'utilisateur",
                "cheminVueBody" => "utilisateur/detail.php"
            ]);
        }
    }

    public static function supprimer(string $idUtilisateur) : RedirectResponse
    {
        $utilisateurRepository = new UtilisateurRepository();
        $deleteSuccessful = $utilisateurRepository->supprimer($idUtilisateur);
        $utilisateurs = $utilisateurRepository->recuperer();
        if ($deleteSuccessful) {
            MessageFlash::ajouter("success", "L'utilisateur a bien été supprimé !");
            return ControleurUtilisateur::rediriger("communes");
        } else {
            MessageFlash::ajouter("warning", "Login inconnu.");
            return ControleurUtilisateur::rediriger("communes");
        }

    }

    public static function afficherFormulaireCreation(): Response
    {
        return ControleurUtilisateur::afficherVue('vueGenerale.php', [
            "pagetitle" => "Création d'un utilisateur",
            "cheminVueBody" => "utilisateur/formulaireCreation.php",
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function creerDepuisFormulaire(): RedirectResponse
    {
        if (
            isset($_REQUEST['login']) && isset($_REQUEST['prenom']) && isset($_REQUEST['nom'])
            && isset($_REQUEST['mdp']) && isset($_REQUEST['mdp2'])
        ) {
            if ($_REQUEST["mdp"] !== $_REQUEST["mdp2"]) {
                MessageFlash::ajouter("warning", "Mots de passe distincts.");
                return ControleurUtilisateur::rediriger("inscription");
            }

            if (!ConnexionUtilisateur::estAdministrateur()) {
                unset($_REQUEST["estAdmin"]);
            }

            if (!filter_var($_REQUEST["email"], FILTER_VALIDATE_EMAIL)) {
                MessageFlash::ajouter("warning", "Email non valide");
                return ControleurUtilisateur::rediriger("inscription");
            }

            $utilisateur = Utilisateur::construireDepuisFormulaire($_REQUEST);

            VerificationEmail::envoiEmailValidation($utilisateur);

            $utilisateurRepository = new UtilisateurRepository();
            $succesSauvegarde = $utilisateurRepository->ajouter($utilisateur);
            if ($succesSauvegarde) {
                MessageFlash::ajouter("success", "L'utilisateur a bien été créé !");
                return ControleurUtilisateur::rediriger("connexion");
            } else {
                MessageFlash::ajouter("warning", "Login existant.");
                return ControleurUtilisateur::rediriger("inscription");
            }
        } else {
            MessageFlash::ajouter("danger", "Login, nom, prenom ou mot de passe manquant.");
            return ControleurUtilisateur::rediriger("inscription");
        }
    }

    public static function afficherFormulaireMiseAJour(string $idUtilisateur): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);
        if ($utilisateur === null) {
            MessageFlash::ajouter("danger", "Login inconnu.");
            return ControleurUtilisateur::rediriger("utilisateur", "afficherListe");
        }
        if (!(ConnexionUtilisateur::estUtilisateur($idUtilisateur) || ConnexionUtilisateur::estAdministrateur())) {
            MessageFlash::ajouter("danger", "La mise à jour n'est possible que pour l'utilisateur connecté ou un administrateur");
            return ControleurUtilisateur::rediriger("utilisateur", "afficherListe");
        }

        $loginHTML = htmlspecialchars($idUtilisateur);
        $prenomHTML = htmlspecialchars($utilisateur->getPrenom());
        $nomHTML = htmlspecialchars($utilisateur->getNom());
        $emailHTML = htmlspecialchars($utilisateur->getEmail());
        return ControleurUtilisateur::afficherVue('vueGenerale.php', [
            "pagetitle" => "Mise à jour d'un utilisateur",
            "cheminVueBody" => "utilisateur/formulaireMiseAJour.php",
            "loginHTML" => $loginHTML,
            "prenomHTML" => $prenomHTML,
            "nomHTML" => $nomHTML,
            "emailHTML" => $emailHTML,
            "estAdmin" => $utilisateur->getEstAdmin(),
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);

    }

    public static function mettreAJour(): RedirectResponse
    {
        if (!(isset($_REQUEST['login']) && isset($_REQUEST['prenom']) && isset($_REQUEST['nom'])
            && isset($_REQUEST['mdp']) && isset($_REQUEST['mdp2']) && isset($_REQUEST['mdpAncien'])
            && isset($_REQUEST['email'])
        )) {
            MessageFlash::ajouter("danger", "Login, nom, prenom, email ou mot de passe manquant.");
            return ControleurUtilisateur::rediriger("utilisateurs");
        }

        if ($_REQUEST["mdp"] !== $_REQUEST["mdp2"]) {
            MessageFlash::ajouter("warning", "Mots de passe distincts.");
            return ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireMiseAJour", ["login" => $_REQUEST["login"]]);
        }

        if (!(ConnexionUtilisateur::estConnecte($_REQUEST["login"]) || ConnexionUtilisateur::estAdministrateur())) {
            MessageFlash::ajouter("danger", "La mise à jour n'est possible que pour l'utilisateur connecté ou un administrateur");
            return ControleurUtilisateur::rediriger("utilisateur", "afficherListe");
        }

        if (!filter_var($_REQUEST["email"], FILTER_VALIDATE_EMAIL)) {
            MessageFlash::ajouter("warning", "Email non valide");
            return ControleurUtilisateur::rediriger("utilisateur", "afficherFormulaireMiseAJour", ["login" => $_REQUEST["login"]]);
        }

        $utilisateurRepository = new UtilisateurRepository();
        /** @var Utilisateur $utilisateur */
        $utilisateur = $utilisateurRepository->recupererParClePrimaire($_REQUEST['login']);

        if ($utilisateur == null) {
            MessageFlash::ajouter("danger", "Login inconnu");
            return ControleurUtilisateur::rediriger("utilisateur", "afficherListe");
        }

        if (!MotDePasse::verifier($_REQUEST["mdpAncien"], $utilisateur->getMdpHache())) {
            MessageFlash::ajouter("warning", "Ancien mot de passe erroné.");
            return ControleurUtilisateur::rediriger("afficherFormulaireMiseAJour", ["login" => $_REQUEST["login"]]);
        }

        $utilisateur->setNom($_REQUEST["nom"]);
        $utilisateur->setPrenom($_REQUEST["prenom"]);
        $utilisateur->setMdpHache($_REQUEST["mdp"]);

        if (ConnexionUtilisateur::estAdministrateur()) {
            $utilisateur->setEstAdmin(isset($_REQUEST["estAdmin"]));
        }

        if ($_REQUEST["email"] !== $utilisateur->getEmail()) {
            $utilisateur->setEmailAValider($_REQUEST["email"]);
            $utilisateur->setNonce(MotDePasse::genererChaineAleatoire());

            VerificationEmail::envoiEmailValidation($utilisateur);
        }

        $utilisateurRepository->mettreAJour($utilisateur);

        MessageFlash::ajouter("success", "L'utilisateur a bien été modifié !");
        return ControleurUtilisateur::rediriger("utilisateurs");
    }

    public static function afficherFormulaireConnexion(): Response
    {
        return ControleurUtilisateur::afficherVue('vueGenerale.php', [
            "pagetitle" => "Formulaire de connexion",
            "cheminVueBody" => "utilisateur/formulaireConnexion.php",
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function connecter(): RedirectResponse
    {
        if (!(isset($_REQUEST['login']) && isset($_REQUEST['mdp']))) {
            MessageFlash::ajouter("danger", "Login ou mot de passe manquant.");
            return ControleurUtilisateur::rediriger("connexion");
        }
        $utilisateurRepository = new UtilisateurRepository();
        /** @var Utilisateur $utilisateur */
        $utilisateur = $utilisateurRepository->recupererParClePrimaire($_REQUEST["login"]);

        if ($utilisateur == null) {
            MessageFlash::ajouter("warning", "Login inconnu.");
            return ControleurUtilisateur::rediriger("connexion");
        }

        if (!MotDePasse::verifier($_REQUEST["mdp"], $utilisateur->getMdpHache())) {
            MessageFlash::ajouter("warning", "Mot de passe incorrect.");
            return ControleurUtilisateur::rediriger("connexion");
        }

        if (!VerificationEmail::aValideEmail($utilisateur)) {
            MessageFlash::ajouter("warning", "Adresse email non validée.");
            VerificationEmail::traiterEmailValidation($utilisateur->getLogin(), $utilisateur->getNonce());
            return ControleurUtilisateur::rediriger("connexion");
        }

        ConnexionUtilisateur::connecter($utilisateur->getLogin());
        return ControleurUtilisateur::rediriger("detailUtilisateur", ["idUtilisateur" => $utilisateur->getLogin()]);
    }

    public static function deconnecter(): RedirectResponse
    {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("danger", "Utilisateur non connecté.");
            return ControleurUtilisateur::rediriger("utilisateurs");
        }
        ConnexionUtilisateur::deconnecter();
        MessageFlash::ajouter("success", "L'utilisateur a bien été déconnecté.");
        return ControleurUtilisateur::rediriger("utilisateurs");
    }

    public static function validerEmail(string $idUtilisateur, string $nonce) : RedirectResponse
    {
        $succesValidation = VerificationEmail::traiterEmailValidation($idUtilisateur, $nonce);

        if (!$succesValidation) {
            MessageFlash::ajouter("warning", "Email de validation incorrect.");
            return ControleurUtilisateur::rediriger("utilisateurs");
        }

        $utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);
        MessageFlash::ajouter("warning", "Validation d'email réussie");
        return  ControleurUtilisateur::rediriger("detailUtilisateur", ["idUtilisateur" => $idUtilisateur]);

    }
}
