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

    public static function getRoute($lat, $lng): Response
    {
        $sql = "SELECT *
        FROM noeud_commune
        WHERE ST_DWithin(
          noeud_commune.geom,
          ST_GeomFromText('POINT($lng $lat)', 4326),
          100
        )
        ORDER BY ST_Distance(
          noeud_commune.geom,
          ST_GeomFromText('POINT($lng $lat)', 4326)
        )
        LIMIT 1;";

        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->query($sql);

        $tab = [];
        foreach ($pdoStatement as $a) {
            $tab[] = $a;
        }

        return ControleurRequete::afficherVue(
            'noeudCommune/requeteRoute.php',
            [
                "tab" => json_encode($tab),
                "pagetitle" => "Plus court chemin"
            ]
        );
    }
}
