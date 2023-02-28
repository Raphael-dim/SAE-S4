<?php

namespace App\PlusCourtChemin\Modele\Repository;

use App\PlusCourtChemin\Modele\DataObject\AbstractDataObject;
use App\PlusCourtChemin\Modele\DataObject\TronconRoute;

class TronconRouteRepository extends AbstractRepository
{

    public function construireDepuisTableau(array $noeudRoutierTableau): TronconRoute
    {
        return new TronconRoute(
            $noeudRoutierTableau["gid"],
            $noeudRoutierTableau["id_rte500"],
            $noeudRoutierTableau["sens"],
            $noeudRoutierTableau["num_route"] ?? "",
            (float) $noeudRoutierTableau["longueur"],
            $noeudRoutierTableau["st_x_s"],
            $noeudRoutierTableau["st_y_s"],
            $noeudRoutierTableau["st_x_e"],
            $noeudRoutierTableau["st_y_e"],
            null
        );
    }

    protected function getNomTable(): string
    {
        return 'troncon_route';
    }

    protected function getNomClePrimaire(): string
    {
        return 'gid';
    }

    protected function getNomsColonnes(): array
    {
        return ["gid", "id_rte500", "sens", "num_route", "longueur", "ST_X(st_startpoint(geom)) as st_x_s", "ST_Y(st_startpoint(geom)) as st_y_s","ST_X(st_endpoint(geom)) as st_x_e","ST_Y(st_endpoint(geom)) as st_y_e"];
    }

    // On bloque l'ajout, la màj et la suppression pour ne pas modifier la table
    // Normalement, j'ai restreint l'accès à SELECT au niveau de la BD
    public function supprimer(string $valeurClePrimaire): bool
    {
        return false;
    }

    public function mettreAJour(AbstractDataObject $object): void
    {
        return;
    }

    public function ajouter(AbstractDataObject $object): bool
    {
        return false;
    }

}
