<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;
use Symfony\Component\HttpFoundation\Response;

class ControleurRequeteVille extends ControleurGenerique
{
    public static function getVille($ville): Response
    {
        require "../src/Modele/Repository/NoeudCommuneRepository.php";

        if ($ville == "") {
            return ControleurRequeteVille::afficherVue(
                'noeudCommune/requeteVille.php',
                [
                    "tab" => json_encode($tab),
                    "pagetitle" => "Plus court chemin"
                ]

            );
        } else {
            // lancement de la requête SQL avec selectByName et
            // récupération du résultat de la requête SQL
            $tab = (new NoeudCommuneRepository)->recupererWhere(["nom_comm" => "LIKE LOWER('" . $ville . "%')"], 20);

            // affichage en format JSON du résultat précédent
            return ControleurRequeteVille::afficherVue(
                'noeudCommune/requeteVille.php',
                [
                    "tab" => json_encode($tab),
                    "pagetitle" => "Plus court chemin"
                ]
            );
        }
    }
}
