<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Lib\Conteneur;
use App\PlusCourtChemin\Lib\MessageFlash;
use Psr\Log\NullLogger;

class ControleurGenerique
{

    protected static function afficherVue(string $cheminVue, array $parametres = []): void
    {
        extract($parametres);
        $messagesFlash = MessageFlash::lireTousMessages();
        require __DIR__ . "/../vue/$cheminVue";
    }

    // https://stackoverflow.com/questions/768431/how-do-i-make-a-redirect-in-php
    protected static function rediriger(string $nomRoute, $tab = NULL): void
    {
        if (!is_null($tab)) {
            $url = Conteneur::recupererService('generateurUrl')->generate($nomRoute, $tab);
        } else {
            $url = Conteneur::recupererService('generateurUrl')->generate($nomRoute);
        }
        header("Location: " . $url);
        exit();
    }

    public static function afficherErreur($errorMessage = "", $controleur = ""): void
    {
        $errorMessageView = "Problème";
        if ($controleur !== "")
            $errorMessageView .= " avec le contrôleur $controleur";
        if ($errorMessage !== "")
            $errorMessageView .= " : $errorMessage";

        ControleurGenerique::afficherVue('vueGenerale.php', [
            "pagetitle" => "Problème",
            "cheminVueBody" => "erreur.php",
            "errorMessage" => $errorMessageView
        ]);
    }
}
