<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Modele\Repository\ConnexionBaseDeDonnees;
use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;
use Symfony\Component\HttpFoundation\Response;

class ControleurRequete extends ControleurGenerique
{
    public static function getVille($ville): Response
    {

        if ($ville == "") {
            return ControleurRequete::afficherVue(
                'noeudCommune/requeteVille.php',
                [
                    "tab" => json_encode(),
                    "pagetitle" => "Plus court chemin"
                ]

            );
        } else {
            // lancement de la requête SQL avec selectByName et
            // récupération du résultat de la requête SQL
            $tab = (new NoeudCommuneRepository)->recupererWhere(["nom_comm" => "LIKE LOWER('" . $ville . "%')"], 20);

            // affichage en format JSON du résultat précédent
            return ControleurRequete::afficherVue(
                'noeudCommune/requeteVille.php',
                [
                    "tab" => json_encode($tab),
                    "pagetitle" => "Plus court chemin"
                ]
            );
        }
    }

    public static function getVilleAvecLatlng($lat, $lng): Response
    {
        $tab = NoeudCommuneRepository::getCommuneAvecLatLng($lat, $lng);

        return ControleurRequete::afficherVue(
            'noeudCommune/requeteRoute.php',
            [
                "tab" => $tab,
                "pagetitle" => "Plus court chemin"
            ]
        );
    }
}
