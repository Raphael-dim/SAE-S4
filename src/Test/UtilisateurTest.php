<?php

use App\PlusCourtChemin\Modele\Repository\UtilisateurRepository;
use App\PlusCourtChemin\Service\Exception\ServiceException;
use App\PlusCourtChemin\Service\UtilisateurService;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{
    private $UtilisateurRepository;

    //On réinitialise l'ensemble avant chaque test
    protected function setUp(): void
    {
        $this->UtilisateurRepository = new UtilisateurRepository();
    }

    /* TEST CREATION */

    /*public function testCreerHTMLCharUtilisateur(){
        try {
            $result = UtilisateurService::verificationCreation("<h1>utilisateur1</h1>","<strong>Joseph</strong>","<!--Dupont-->","123456789","123456789","toto@gmail.com");
        } catch (ServiceException $e) {
            $this->assertTrue(true, $e->getMessage());
            return;
        }

        $this->assertNotNull($result, "L'utilisateur est null");
        $this->assertNotEmpty($result, "L'utilisateur est vide");
        $this->assertTrue($result->getLogin() == "&lt;h1&gt;utilisateur1&lt;/h1&gt;", "Le chemin n'est pas un array");
        $this->assertTrue($result->getPrenom() == "&lt;strong&gt;Joseph&lt;/strong&gt;", "Le chemin n'est pas un array");
        $this->assertTrue($result->getNom() == "&lt;!--Dupont--&gt;", "Le chemin n'est pas un array");
    }*/

    public function testCreerUtilisateurMauvaisMDP(){
        try {
            $result = UtilisateurService::verificationCreation("utilisateur1","Joseph","Dupont","123456789","password","toto@gmail.com");
        } catch (ServiceException $e) {
            echo "ERREUR GERE, " . $e->getMessage();
            $this->assertTrue(true, $e->getMessage());
            return;
        }

        $this->assertNotNull($result, "L'utilisateur est null");
        $this->assertNotEmpty($result, "L'utilisateur est vide");
        $this->assertTrue($result->getLogin() == "password" || $result->getLogin() == "123456789", "/!\ Utilisateur créer malgré mot de passe différent");
    }

    public function testCreerUtilisateurMauvaisMail(){
        try {
            $result = UtilisateurService::verificationCreation("utilisateur1","Joseph","Dupont","123456789","123456789","toto.gmail.com");
        } catch (ServiceException $e) {
            echo "ERREUR GERE, " . $e->getMessage();
            $this->assertTrue(true, $e->getMessage());
            return;
        }

        $this->assertNotNull($result, "L'utilisateur est null");
        $this->assertNotEmpty($result, "L'utilisateur est vide");
        $this->assertTrue(str_contains($result->getEmail(), '@') && str_contains($result->getEmail(), '.'), "/!\ Utilisateur créer malgré mot de passe différent");
    }

    /* TEST CONNEXION */
    public function testConnexionUtilisateurInexistant(){
        try {
            $result = UtilisateurService::verificationConnexion("inexistant","eee");
        } catch (ServiceException $e) {
            echo "ERREUR GERE, " . $e->getMessage();
            $this->assertTrue(true, $e->getMessage());
            return;
        }

        $this->assertNotNull($result, "L'utilisateur est null");
        $this->assertNotEmpty($result, "L'utilisateur est vide");
    }

    public function testConnexionUtilisateurMauvaisMDP(){
        try {
            $result = UtilisateurService::verificationConnexion("makloufiy","paslebon");
        } catch (ServiceException $e) {
            echo "ERREUR GERE, " . $e->getMessage();
            $this->assertTrue(true, $e->getMessage());
            return;
        }

        $this->assertNotNull($result, "L'utilisateur est null");
        $this->assertNotEmpty($result, "L'utilisateur est vide");
    }
}
