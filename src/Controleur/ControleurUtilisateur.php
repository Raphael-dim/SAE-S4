<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Configuration\Configuration;
use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Lib\Conteneur;
use App\PlusCourtChemin\Lib\MessageFlash;
use App\PlusCourtChemin\Lib\MotDePasse;
use App\PlusCourtChemin\Lib\VerificationEmail;
use App\PlusCourtChemin\Modele\DataObject\Utilisateur;
use App\PlusCourtChemin\Modele\Repository\UtilisateurRepository;
use App\PlusCourtChemin\Service\Exception\ServiceException;
use App\PlusCourtChemin\Service\UtilisateurService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\logicalNot;

class ControleurUtilisateur extends ControleurGenerique
{

    public static function afficherListe(): Response
    {
        try {
            $utilisateurs = UtilisateurService::recupererUtilisateurs();
        } catch (ServiceException $e) {
            MessageFlash::ajouter('danger', $e->getMessage());
            return ControleurNoeudCommune::rediriger("communes");
        }

        return ControleurUtilisateur::afficherTwig('utilisateur/liste.html.twig',[
            "page_title" => "Liste des utilisateurs",
            "utilisateurs" => $utilisateurs,
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function afficherDetail($idUtilisateur): Response
    {
        try {
            $utilisateur = UtilisateurService::recupererDetailUtilisateur($idUtilisateur);
        } catch (ServiceException $e) {
            MessageFlash::ajouter('danger', $e->getMessage());
            return ControleurNoeudCommune::rediriger("utilisateurs");
        }

        return ControleurUtilisateur::afficherTwig('utilisateur/detail.html.twig',[
            "page_title" => "Détail de l'utilisateur",
            "trajets" => $utilisateur->getTrajets(),
            "utilisateur" => $utilisateur,
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function supprimer(string $idUtilisateur): RedirectResponse|Response
    {
        if (ConnexionUtilisateur::getLoginUtilisateurConnecte() == $idUtilisateur) {
            $message = "Êtes vous sûr de vouloir supprimer votre compte ?";
        } else {
            $message = "Êtes vous sûr de vouloir supprimer ce compte ?";
        }
        $conteneur = Conteneur::recupererService('generateurUrl');
        $utilisateurRepository = new UtilisateurRepository();
        if (ConnexionUtilisateur::getLoginUtilisateurConnecte() != $idUtilisateur) {
            MessageFlash::ajouter("warning", "Vous devez être connecté pour supprimer votre compte.");
            return ControleurUtilisateur::rediriger("utilisateurs");
        }
        if (!isset($_POST["cancel"]) && !isset($_POST["confirm"])) {
            return ControleurGenerique::afficherVue('vueGenerale.php', ["pagetitle" => "Demande de confirmation ",
                "cheminVueBody" => "confirm.php",
                "url" => $conteneur->generate("supprimerUtilisateur", ["idUtilisateur" => $idUtilisateur]),
                "mdp" => true,
                "message" => $message]);
        } else if (isset($_POST["cancel"])) {
            return ControleurGenerique::rediriger("detailUtilisateur", ["idUtilisateur" => $idUtilisateur]);
        } else if (isset($_POST["confirm"])) {
            /** @var Utilisateur $utilisateur */
            $utilisateur = $utilisateurRepository->recupererParClePrimaire($idUtilisateur);
            if (!MotDePasse::verifier($_POST['mdp'], $utilisateur->getMdpHache())) {
                MessageFlash::ajouter('warning', 'Mot de passe incorrect');
                return ControleurGenerique::afficherVue('vueGenerale.php', ["pagetitle" => "Demande de confirmation ",
                    "cheminVueBody" => "confirm.php",
                    "url" => $conteneur->generate("supprimerUtilisateur", ["idUtilisateur" => $idUtilisateur]),
                    "mdp" => true,
                    "message" => $message]);
            } else {
                $deleteSuccessful = $utilisateurRepository->supprimer($idUtilisateur);
                $utilisateurs = $utilisateurRepository->recuperer();
                if ($deleteSuccessful) {
                    MessageFlash::ajouter("success", "L'utilisateur a bien été supprimé !");
                    self::deconnecter();
                    return ControleurUtilisateur::rediriger("communes");
                } else {
                    MessageFlash::ajouter("warning", "Login inconnu.");
                    return ControleurUtilisateur::rediriger("utilisateurs");
                }
            }
        }
        return ControleurGenerique::rediriger("communes");
    }

    public static function afficherFormulaireCreation(): Response
    {
        return ControleurUtilisateur::afficherTwig('utilisateur/creation.html.twig',[
            "page_title" => "Création d'un utilisateur",
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function creerDepuisFormulaire(): RedirectResponse
    {
        if (
            isset($_REQUEST['login']) && isset($_REQUEST['prenom']) && isset($_REQUEST['nom'])
            && isset($_REQUEST['mdp']) && isset($_REQUEST['mdp2'])
        ) {
            if (!ConnexionUtilisateur::estAdministrateur()) {
                unset($_REQUEST["estAdmin"]);
            }

            try {
                $utilisateur = UtilisateurService::verificationCreation($_REQUEST['login'], $_REQUEST['prenom'], $_REQUEST['nom'], $_REQUEST["mdp"], $_REQUEST["mdp2"], $_REQUEST['email']);
            } catch (ServiceException $e) {
                MessageFlash::ajouter('danger', $e->getMessage());
                return ControleurNoeudCommune::rediriger("creerDepuisFormulaire");
            }

            (new UtilisateurRepository())->ajouter($utilisateur);

            VerificationEmail::envoiEmailValidation($utilisateur);

            MessageFlash::ajouter("success", "L'utilisateur a bien été créé !");
            return ControleurUtilisateur::rediriger("connexion");

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
            return ControleurUtilisateur::rediriger("utilisateurs");
        }
        if (!(ConnexionUtilisateur::estUtilisateur($idUtilisateur) || ConnexionUtilisateur::estAdministrateur())) {
            MessageFlash::ajouter("danger", "La mise à jour n'est possible que pour l'utilisateur connecté ou un administrateur");
            return ControleurUtilisateur::rediriger("utilisateurs");
        }

        return ControleurUtilisateur::afficherTwig('utilisateur/maj.html.twig',[
            "page_title" => "Mise à jour d'un utilisateur",
            "login" => $idUtilisateur,
            "prenom" => $utilisateur->getPrenom(),
            "nom" => $utilisateur->getNom(),
            "email" => $utilisateur->getEmail(),
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

        try {
            $utilisateur = UtilisateurService::verificationMiseAJour($_REQUEST['login'], $_REQUEST['prenom'], $_REQUEST['nom'], $_REQUEST['mdp'], $_REQUEST['mdp2'], $_REQUEST['mdpAncien'], $_REQUEST['email']);
        } catch (ServiceException $e) {
            MessageFlash::ajouter("warning", $e->getMessage());
            return ControleurUtilisateur::rediriger("mettreAJour", ["login" => $_REQUEST["login"]]);
        }

        if (ConnexionUtilisateur::estAdministrateur()) {
            $utilisateur->setEstAdmin(isset($_REQUEST["estAdmin"]));
        }

        if ($_REQUEST["email"] !== $utilisateur->getEmail()) {
            $utilisateur->setEmailAValider($_REQUEST['email']);
            $utilisateur->setNonce(MotDePasse::genererChaineAleatoire());

            VerificationEmail::envoiEmailValidation($utilisateur);
        }

        (new UtilisateurRepository())->mettreAJour($utilisateur);

        MessageFlash::ajouter("success", "L'utilisateur a bien été modifié !");
        return ControleurUtilisateur::rediriger("detailUtilisateur", ["idUtilisateur" => $utilisateur->getLogin()]);
    }

    public static function afficherFormulaireConnexion(): Response
    {
        return ControleurUtilisateur::afficherTwig('utilisateur/connexion.html.twig',[
            "page_title" => "Formulaire de connexion",
            "method" => Configuration::getDebug() ? "get" : "post",
        ]);
    }

    public static function connecter(): RedirectResponse
    {
        if (!(isset($_REQUEST['login']) && isset($_REQUEST['mdp']))) {
            MessageFlash::ajouter('danger', "Login ou mot de passe manquant");
            return ControleurUtilisateur::rediriger("connexion");
        }

        try {
            $utilisateur = UtilisateurService::verificationConnexion($_REQUEST['login'], $_REQUEST['mdp']);
        } catch (ServiceException $e) {
            MessageFlash::ajouter('danger', $e->getMessage());
            if ($e->getCode() === 1) {
                MessageFlash::ajouter('info', "Cliquez <a href='./envoyerMail/" . $_REQUEST['login'] . "'>ici</a> pour renvoyer un mail");
            }
            return ControleurUtilisateur::rediriger("connexion");
        }

        ConnexionUtilisateur::connecter($utilisateur->getLogin());
        return ControleurUtilisateur::rediriger("detailUtilisateur", ["idUtilisateur" => $utilisateur->getLogin()]);
    }

    public static function envoyerMail($idUtilisateur): RedirectResponse
    {
        $utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);
        VerificationEmail::envoiEmailValidation($utilisateur);
        return ControleurUtilisateur::rediriger('connexion');
    }

    public static function deconnecter(): RedirectResponse
    {
        if (!ConnexionUtilisateur::estConnecte()) {
            MessageFlash::ajouter("danger", "Utilisateur non connecté.");
            return ControleurUtilisateur::rediriger("communes");
        }
        ConnexionUtilisateur::deconnecter();
        MessageFlash::ajouter("success", "L'utilisateur a bien été déconnecté.");
        return ControleurUtilisateur::rediriger("communes");
    }

    public static function validerEmail(string $idUtilisateur, string $nonce): RedirectResponse
    {
        $succesValidation = VerificationEmail::traiterEmailValidation($idUtilisateur, $nonce);

        if (!$succesValidation) {
            MessageFlash::ajouter("warning", "Email de validation incorrect.");
            return ControleurUtilisateur::rediriger("utilisateurs");
        }

        //$utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);
        MessageFlash::ajouter("success", "Validation d'email réussie");
        return ControleurUtilisateur::rediriger("detailUtilisateur", ["idUtilisateur" => $idUtilisateur]);
    }
}
