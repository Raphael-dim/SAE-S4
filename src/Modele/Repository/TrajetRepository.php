<?php

namespace App\PlusCourtChemin\Modele\Repository;

use App\PlusCourtChemin\Modele\DataObject\Trajet;

class TrajetRepository extends AbstractRepository
{

    protected function getNomTable(): string
    {
        return "trajet";
    }

    protected function getNomClePrimaire(): string
    {
        return "idtrajet";
    }

    protected function getNomsColonnes(): array
    {
        return ["gid_commune_depart", "gid_commune_arrivee", "loginutilisateur", "date", "idtrajet"];
    }

    protected function construireDepuisTableau(array $objetFormatTableau): Trajet
    {
        return new Trajet($objetFormatTableau['loginutilisateur'], $objetFormatTableau['gid_commune_depart'],
            $objetFormatTableau['gid_commune_arrivee'], $objetFormatTableau['date']);
    }
}