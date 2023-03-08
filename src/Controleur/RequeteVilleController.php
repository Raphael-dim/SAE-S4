<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;

class RequeteVilleController
{
    public static function getVille(){
        require "../src/Modele/Repository/NoeudCommuneRepository.php";

        $ville = $_GET['ville'];

        // lancement de la requête SQL avec selectByName et
        // récupération du résultat de la requête SQL
                $tab = (new NoeudCommuneRepository)->recupererWhere(["nom_comm" => "LIKE LOWER('" . $ville . "%')"]);

        // délai fictif
        // sleep(1);

        // affichage en format JSON du résultat précédent
        echo json_encode($tab);

    }
}