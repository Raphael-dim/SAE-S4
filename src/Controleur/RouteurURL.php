<?php
namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Lib\Conteneur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteurURL
{
    public static function traiterRequete() {
        $requete = Request::createFromGlobals();
        $routes = new RouteCollection();

        // ROUTE POUR AFFICHER TOUTES LES COMMUNES
        $routeFeed = new Route("/", [
            "_controller" => [ControleurNoeudCommune::class, "afficherListe"],
        ]);
        $routes->add("communes", $routeFeed);


        // ROUTE POUR AFFICHER LA PAGE DE CONNEXION
        $routeConnexion = new Route("/connexion", [
            "_controller" => [ControleurUtilisateur::class, "afficherFormulaireConnexion"],
        ]);
        $routeConnexion->setMethods(['GET']);
        $routes->add("afficherFormulaireConnexion", $routeConnexion);


        // ROUTE POUR SE CONNECTER APRES AVOIR SAISI LES DONNEES
        $routeConnecter = new Route("/connexion", [
            "_controller" => [ControleurUtilisateur::class, "connecter"],
        ]);
        $routeConnecter->setMethods(['POST']);
        $routes->add("connexion", $routeConnecter);


        // ROUTE POUR afficherListe des utilisateurs
        $routeAfficherListe = new Route("/utilisateurs", [
            "_controller" => [ControleurUtilisateur::class, "afficherListe"],
        ]);
        $routes->add("utilisateurs", $routeAfficherListe);


        // ROUTE POUR afficherFormulaireCreation de ControleurUtilisateur
        $routeAfficherCreationCompte = new Route("/inscription", [
            "_controller" => [ControleurUtilisateur::class, "afficherFormulaireCreation"],
        ]);
        $routeAfficherCreationCompte->setMethods(['GET']);
        $routes->add("afficherFormulaireCreation", $routeAfficherCreationCompte);

        
        // ROUTE POUR creerDepuisFormulaire de ControleurUtilisateur
        $routeCreationCompte = new Route("/inscription", [
            "_controller" => [ControleurUtilisateur::class, "creerDepuisFormulaire"],
        ]);
        $routeCreationCompte->setMethods(['POST']);
        $routes->add("creerDepuisFormulaire", $routeCreationCompte);


        // ROUTE POUR afficherDetail de ControleurNoeudCommune
        $routeDetailCommune = new Route("/detailCommune/{idCommune}", [
            "_controller" => [ControleurNoeudCommune::class, "afficherDetail"],
        ]);
        $routes->add("afficherDetail", $routeDetailCommune);


        // ROUTE POUR plusCourtChemin de ControleurNoeudCommune
        $routePlusCourtChemin = new Route("/plusCourtChemin", [
            "_controller" => [ControleurNoeudCommune::class, "plusCourtChemin"],
        ]);
        $routePlusCourtChemin->setMethods(['GET']);
        $routes->add("plusCourtChemin", $routePlusCourtChemin);

        // ROUTE POUR calculer le plusCourtChemin de ControleurNoeudCommune (POST)UR
        $routePlusCourtChemin = new Route("/calculer", [
            "_controller" => [ControleurNoeudCommune::class, "plusCourtChemin"],
        ]);
        $routePlusCourtChemin->setMethods(['POST']);
        $routes->add("calculer", $routePlusCourtChemin); 

        // ROUTE POUR CompletionAuto Villes
        $routeRequeteVille = new Route("/villes",[
            "_controller" => [RequeteVilleController::class, "getVille"],
        ]);
        $routeRequeteVille->setMethods(['GET']);
        $routes->add("autoCompletionVille", $routeRequeteVille);

        $contexteRequete = (new RequestContext())->fromRequest($requete);
        $associateurUrl = new UrlMatcher($routes, $contexteRequete);
        $donneesRoute = $associateurUrl->match($requete->getPathInfo());

        $requete->attributes->add($donneesRoute);

        $resolveurDeControleur = new ControllerResolver();
        $controleur = $resolveurDeControleur->getController($requete);

        $resolveurDArguments = new ArgumentResolver();
        $arguments = $resolveurDArguments->getArguments($requete, $controleur);


        $assistantUrl = new UrlHelper(new RequestStack(), $contexteRequete);
        // $assistantUrl->getAbsoluteUrl("assets/css/styles.css");
        // Renvoie l'URL .../web/assets/css/styles.css, peu importe l'URL courante
        Conteneur::ajouterService("assistantUrl", $assistantUrl);

        $generateurUrl = new UrlGenerator($routes, $contexteRequete);
        // $generateurUrl->generate("submitFeedy");
        // Renvoie ".../web/feedy"
        // $generateurUrl->generate("pagePerso", ["idUser" => 19]);
        // Renvoie ".../web/utilisateur/19"

        Conteneur::ajouterService("generateurUrl", $generateurUrl);

        call_user_func_array($controleur, $arguments);

    }
}