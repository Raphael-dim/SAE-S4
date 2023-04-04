<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Lib\Conteneur;
use App\PlusCourtChemin\Lib\MessageFlash;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ControleurGenerique
{

    // Méthode pour afficher une vue en récupérant les paramètres et les messages flash
    protected static function afficherVue(string $cheminVue, array $parametres = []): Response
    {
        extract($parametres);
        $messagesFlash = MessageFlash::lireTousMessages();
        ob_start();
        require __DIR__ . "/../vue/$cheminVue";
        $corpsReponse = ob_get_clean();
        return new Response($corpsReponse);
    }

    // https://stackoverflow.com/questions/768431/how-do-i-make-a-redirect-in-php
    // Méthode pour rediriger vers une autre page en utilisant une route et des paramètres optionnels
    protected static function rediriger(string $nomRoute, $tab = NULL): RedirectResponse
    {
        if (!is_null($tab)) {
            $url = Conteneur::recupererService('generateurUrl')->generate($nomRoute, $tab);
        } else {
            $url = Conteneur::recupererService('generateurUrl')->generate($nomRoute);
        }
        // header("Location: " . $url);
        // exit();
        return new RedirectResponse($url);
    }


    // Méthode pour afficher une erreur en utilisant une vue générale et un message d'erreur
    public static function afficherErreur($errorMessage = "", $statusCode = 400): Response
    {
        $reponse = ControleurGenerique::afficherVue('vueGenerale.php', [
            "pagetitle" => "Problème",
            "cheminVueBody" => "erreur.php",
            "errorMessage" => $errorMessage
        ]);

        $reponse->setStatusCode($statusCode);
        return $reponse;
    }

    // Méthode pour afficher une vue en utilisant le moteur de template Twig
    protected static function afficherTwig(string $cheminVue, array $parametres = []): Response
    {
        /** @var Environment $twig */
        $twig = Conteneur::recupererService("twig");
        return new Response($twig->render($cheminVue, $parametres));
    }
}
