<?php

namespace App\PlusCourtChemin\Modele\Repository;

use App\PlusCourtChemin\Modele\DataObject\AbstractDataObject;
use App\PlusCourtChemin\Modele\DataObject\NoeudRoutier;
use PDO;

class NoeudRoutierRepository extends AbstractRepository
{
    // Construit un objet NoeudRoutier à partir d'un tableau
    public function construireDepuisTableau(array $noeudRoutierTableau): NoeudRoutier
    {
        return new NoeudRoutier(
            $noeudRoutierTableau["gid"],
            $noeudRoutierTableau["id_rte500"],
            $noeudRoutierTableau["st_x"],
            $noeudRoutierTableau["st_y"],
        );
    }

    // Renvoie le nom de la table correspondante dans la base de données
    protected function getNomTable(): string
    {
        return 'noeud_routier';
    }

    // Renvoie le nom de la clé primaire de la table
    protected function getNomClePrimaire(): string
    {
        return 'gid';
    }

    // Renvoie le nom des colonnes à sélectionner dans la requête SQL
    protected function getNomsColonnes(): array
    {
        return ["gid", "id_rte500", "ST_X(geom)", "ST_Y(geom)"];
    }

    // On bloque l'ajout, la màj et la suppression pour ne pas modifier la table
    // Normalement, j'ai restreint l'accès à SELECT au niveau de la BD
    public function supprimer(string $valeurClePrimaire): bool
    {
        return false;
    }

    // Bloque la mise à jour pour ne pas modifier la table
    public function mettreAJour(AbstractDataObject $object): void
    {
        return;
    }

    public function ajouter(AbstractDataObject $object): bool
    {
        return false;
    }


    /**
     * Renvoie le tableau des voisins d'un noeud routier
     * Chaque voisin est un tableau avec les 3 champs
     * `noeud_routier_gid`, `troncon_gid`, `longueur`
     *
     * @param int $noeudRoutierDepartGid
     * @param int $noeudRoutierArriveeGid
     * @return String[][]
     */
    public static function getShortestPathAstar(int $noeudRoutierDepartGid, int $noeudRoutierArriveeGid): array
    {
        $requeteSQL =
            "SELECT noeud_arrivee_gid as noeud_routier_gid,troncon_gid,r.longueur, ST_X(noeud_depart_geom) as lat,
                ST_Y(noeud_depart_geom) as lon, noeud_depart_gid, agg_cost as distance
            FROM pgr_astar(
              'SELECT troncon_gid AS id,
                  noeud_depart_gid AS source, 
                  noeud_arrivee_gid AS target,
                  longueur AS cost,
                  ST_X(noeud_arrivee_geom) AS x1,
                  ST_Y(noeud_arrivee_geom) AS y1,
                  ST_X(noeud_depart_geom) AS x2,
                  ST_Y(noeud_depart_geom) AS y2
                FROM relation'," .
            $noeudRoutierDepartGid . ", " . $noeudRoutierArriveeGid . ",
              directed => false
            ) AS a
            JOIN relation AS r ON (a.edge = r.troncon_gid)";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($requeteSQL);
        $pdoStatement->execute();
        return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
    }
}