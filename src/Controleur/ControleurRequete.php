<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;
use Symfony\Component\HttpFoundation\Response;

class ControleurRequete extends ControleurGenerique
{

    /**
     * Cette méthode permet de récupérer les noms des communes en fonction d'un préfixe de ville donné
     *
     * @param string $ville Le préfixe de ville pour lequel on veut récupérer les noms des communes
     * @return Response La réponse HTTP contenant le résultat de la requête sous forme de tableau JSON
     */
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

    /**
     * Cette méthode permet de récupérer le nom de la commune la plus proche d'une latitude et longitude données
     *
     * @param float $lat La latitude pour laquelle on veut récupérer le nom de la commune la plus proche
     * @param float $lng La longitude pour laquelle on veut récupérer le nom de la commune la plus proche
     * @return Response La réponse HTTP contenant le résultat de la requête sous forme de tableau JSON
     */
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
