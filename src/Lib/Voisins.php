<?php

namespace App\PlusCourtChemin\Lib;

use App\PlusCourtChemin\Modele\DataObject\Voisin;
use App\PlusCourtChemin\Modele\HTTP\Session;
use App\PlusCourtChemin\Modele\Repository\ConnexionBaseDeDonnees;

class Voisins
{
    public static function getVoisins(): array
    {
        $sql = "select * from relation";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($sql);
        $pdoStatement->execute();
        $voisins = [];
        foreach ($pdoStatement as $objetFormatTableau) {
            $voisins[] = new Voisin($objetFormatTableau['noeud_arrivee_geom'], $objetFormatTableau['noeud_arrivee_gid'],
                $objetFormatTableau['noeud_depart_geom'], $objetFormatTableau['noeud_depart_gid'],
                $objetFormatTableau['troncon_gid'], $objetFormatTableau['longueur']);
        }

        return $voisins;
    }

}
