<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Lib\Conteneur;
use App\PlusCourtChemin\Lib\MessageFlash;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class RouteurURL
{
    public static function traiterRequete()
    {
        $requete = Request::createFromGlobals();
        $routes = new RouteCollection();

        // ROUTE POUR AFFICHER TOUTES LES COMMUNES
        $routeCommunes= new Route("/communes", [
            "_controller" => [ControleurNoeudCommune::class, "afficherListe"],
        ]);
        $routes->add("communes", $routeCommunes);


        $routeCommunesPost= new Route("/communesKeyWord", [
            "_controller" => [ControleurNoeudCommune::class, "afficherListe"],
        ]);
        $routeCommunesPost->setMethods(['POST']);
        $routes->add("communesKeyWord", $routeCommunesPost);


        $route = new Route("/", [
            "_controller" => [ControleurNoeudCommune::class, "afficherListe"],
        ]);
        $routes->add("communes", $route);


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
        $routes->add("detailCommune", $routeDetailCommune);


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
        //$routeRequeteVille = new Route("/villes", [
        //    "_controller" => [ControleurRequete::class, "getVille"],
        //]);
        //$routeRequeteVille->setMethods(['GET']);
        //$routes->add("autoCompletionVille", $routeRequeteVille);


        // ROUTE POUR afficherDetail ControleurUtilisateur
        $routeDetailUtilisateur = new Route("/detailUtilisateur/{idUtilisateur}", [
            "_controller" => [ControleurUtilisateur::class, "afficherDetail"],
        ]);
        $routeDetailUtilisateur->setMethods(['GET']);
        $routes->add("detailUtilisateur", $routeDetailUtilisateur);


        // ROUTE POUR deconnecter de ControleurUtilisateur
        $routeDeconnecter = new Route("/deconnecter", [
            "_controller" => [ControleurUtilisateur::class, "deconnecter"],
        ]);
        $routeDeconnecter->setMethods(['GET']);
        $routes->add("deconnecter", $routeDeconnecter);


        // ROUTE POUR afficherFormulaireMiseAJour de ControleurUtilisateur
        $afficherFormulaireMiseAJour = new Route("/mettreAJour/{idUtilisateur}", [
            "_controller" => [ControleurUtilisateur::class, "afficherFormulaireMiseAJour"],
        ]);
        $afficherFormulaireMiseAJour->setMethods(['GET']);
        $routes->add("formulaireMiseAJour", $afficherFormulaireMiseAJour);


        // ROUTE POUR mettreAJour de ControleurUtilisateur
        $mettreAJour = new Route("/mettreAJour", [
            "_controller" => [ControleurUtilisateur::class, "mettreAJour"],
        ]);
        $mettreAJour->setMethods(['POST']);
        $routes->add("mettreAJour", $mettreAJour);


        // ROUTE POUR deconnecter de ControleurUtilisateur
        $supprimerUtilisateur = new Route("/supprimerUtilisateur/{idUtilisateur}", [
            "_controller" => [ControleurUtilisateur::class, "supprimer"],
        ]);
        $supprimerUtilisateur->setMethods(['GET', 'POST']);
        $routes->add("supprimerUtilisateur", $supprimerUtilisateur);


        // ROUTE POUR validerEmail de ControleurUtilisateur
        $validerEmail = new Route("/validerEmail/{idUtilisateur}/{nonce}", [
            "_controller" => [ControleurUtilisateur::class, "validerEmail"],
        ]);
        $routes->add("validerEmail", $validerEmail);


        // ROUTE POUR chercherVille de ControleurRequeteVille
        $chercherVille = new Route("/chercherVille/{ville}", [
            "_controller" => [ControleurRequete::class, "getVille"],
        ]);
        $routes->add("chercherVille", $chercherVille);


        $requeteVilleCoordonnees = new Route("/chercherVilleCoor/{lat}/{lng}", [
            "_controller" => [ControleurRequete::class, "getVilleAvecLatlng"],
        ]);
        $routes->add("chercherVilleCoor", $requeteVilleCoordonnees);


        $envoyerMail = new Route("/envoyerMail/{idUtilisateur}", [
            "_controller" => [ControleurUtilisateur::class, "envoyerMail"],
        ]);
        $routes->add("envoyerMail", $envoyerMail);



        $contexteRequete = (new RequestContext())->fromRequest($requete);
        $associateurUrl = new UrlMatcher($routes, $contexteRequete);


        $assistantUrl = new UrlHelper(new RequestStack(), $contexteRequete);
        Conteneur::ajouterService("assistantUrl", $assistantUrl);

        $generateurUrl = new UrlGenerator($routes, $contexteRequete);
        Conteneur::ajouterService("generateurUrl", $generateurUrl);

        $twigLoader = new FilesystemLoader(__DIR__ . '/../vue/');
        $twig = new Environment(
            $twigLoader,
            [
                'autoescape' => 'html',
                'strict_variables' => true
            ]
        );
        $twig->addFunction(new TwigFunction("route", [$generateurUrl, "generate"]));
        $twig->addFunction(new TwigFunction("asset", [$assistantUrl, "getAbsoluteUrl"]));

        Conteneur::ajouterService("twig", $twig);

        try {
            $donneesRoute = $associateurUrl->match($requete->getPathInfo());
            $requete->attributes->add($donneesRoute);
            $resolveurDeControleur = new ControllerResolver();
            $controleur = $resolveurDeControleur->getController($requete);
            $resolveurDArguments = new ArgumentResolver();
            $arguments = $resolveurDArguments->getArguments($requete, $controleur);
        } catch (ResourceNotFoundException $exception) {
            $reponse = ControleurGenerique::afficherErreur($exception->getMessage(), 404);
            $reponse->send();
            exit();

        } catch (MethodNotAllowedException $exception) {
            $reponse = ControleurGenerique::afficherErreur($exception->getMessage(), 405);
            $reponse->send();
            exit();

        } catch (Exception $exception) {
            $reponse = ControleurGenerique::afficherErreur($exception->getMessage());
            $reponse->send();
            exit();
        }

        $twig->addGlobal('connectedUser', ConnexionUtilisateur::getLoginUtilisateurConnecte());
        $twig->addGlobal('adminUser', ConnexionUtilisateur::getLoginUtilisateurConnecte());
        $twig->addGlobal('messagesFlash', MessageFlash::lireTousMessages());

        $reponse = call_user_func_array($controleur, $arguments);
        $reponse->send();
    }
}