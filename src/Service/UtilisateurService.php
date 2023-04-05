<?php

namespace App\PlusCourtChemin\Service;

use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Lib\MotDePasse;
use App\PlusCourtChemin\Lib\VerificationEmail;
use App\PlusCourtChemin\Modele\DataObject\Utilisateur;
use App\PlusCourtChemin\Modele\Repository\UtilisateurRepository;
use App\PlusCourtChemin\Service\Exception\ServiceException;

class UtilisateurService
{
    static function recupererUtilisateurs() {

        if(ConnexionUtilisateur::estAdministrateur()){
            $utilisateurs = (new UtilisateurRepository())->recuperer();
        }else{
            throw new ServiceException("Vous n'avez pas les droits d'accès a cette page");
        }
        return $utilisateurs;
    }

    static function recupererDetailUtilisateur($idUtilisateur) {

        $utilisateur = (new UtilisateurRepository())->recupererParClePrimaire($idUtilisateur);
        if ($utilisateur === null) {
            throw new ServiceException("Login inconnu.");
        }
        return $utilisateur;
    }

    static function verificationConnexion($login, $password){

        $utilisateurRepository = new UtilisateurRepository();
        /** @var Utilisateur $utilisateur */
        $utilisateur = $utilisateurRepository->recupererParClePrimaire($login);

        if ($utilisateur == null) {
            throw new ServiceException("Login inconnu");
        }

        if (!MotDePasse::verifier($password, $utilisateur->getMdpHache())) {
            throw new ServiceException("Mot de passe incorrect");
        }

        if (!VerificationEmail::aValideEmail($utilisateur)) {
            throw new ServiceException("Adresse email non validé.",1);
        }
        return $utilisateur;
    }

    static function verificationMiseAJour($login, $prenom, $nom, $password, $password2, $passwordOld, $email){

        if ($password !== $password2) {
            throw new ServiceException("Mots de passe distincts.");
        }

        if (!(ConnexionUtilisateur::estConnecte() || ConnexionUtilisateur::estAdministrateur())) {
            throw new ServiceException("La mise à jour n'est possible que pour l'utilisateur connecté ou un administrateur");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException("Email non valide");
        }

        $utilisateurRepository = new UtilisateurRepository();
        /** @var Utilisateur $utilisateur */
        $utilisateur = $utilisateurRepository->recupererParClePrimaire($_REQUEST['login']);

        if ($utilisateur == null) {
            throw new ServiceException("Login inconnu");
        }

        if (!MotDePasse::verifier($passwordOld, $utilisateur->getMdpHache())) {
            throw new ServiceException("Ancien mot de passe erroné.");
        }

        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        $utilisateur->setMdpHache($password);

        return $utilisateur;
    }

    static function verificationCreation($login, $prenom, $nom, $password, $password2, $email){

        if ($password !== $password2) {
            throw new ServiceException("Mots de passe distincts.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ServiceException("Email non valide");
        }

        return Utilisateur::construireDepuisFormulaire($_REQUEST);
    }
}