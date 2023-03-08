<?php

namespace App\PlusCourtChemin\Controleur;

class RouteurQueryString
{
    public static function traiterRequete()
    {
        $action = $_REQUEST['action'] ?? 'afficherListe';


        $controleur = "noeudCommune";
        if (isset($_REQUEST['controleur']))
        $controleur = $_REQUEST['controleur'];

        $controleurClassName = 'App\PlusCourtChemin\Controleur\Controleur' . ucfirst($controleur);

        if (class_exists($controleurClassName)) {
            if (in_array($action, get_class_methods($controleurClassName))) {
                $controleurClassName::$action();
            } else {
                $controleurClassName::afficherErreur("Erreur d'action");
            }
        } else {
            ControleurGenerique::afficherErreur("Erreur de contrôleur");
        }
    }
}
