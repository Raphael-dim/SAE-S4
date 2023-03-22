<?php

namespace App\PlusCourtChemin\Modele\Repository;

use App\PlusCourtChemin\Modele\DataObject\AbstractDataObject;
use App\PlusCourtChemin\Modele\DataObject\NoeudRoutier;
use PDO;

class NoeudRoutierRepository extends AbstractRepository
{

    public function construireDepuisTableau(array $noeudRoutierTableau): NoeudRoutier
    {
        return new NoeudRoutier(
            $noeudRoutierTableau["gid"],
            $noeudRoutierTableau["id_rte500"],
            $noeudRoutierTableau["st_x"],
            $noeudRoutierTableau["st_y"],
            null
        );
    }

    protected function getNomTable(): string
    {
        return 'noeud_routier';
    }

    protected function getNomClePrimaire(): string
    {
        return 'gid';
    }

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
     *
     * Chaque voisin est un tableau avec les 3 champs
     * `noeud_routier_gid`, `troncon_gid`, `longueur`
     *
     * @param int $noeudRoutierGid
     * @return String[][]
     **/
    public function getVoisins(int $noeudRoutierGid): array
    {
        $requeteSQL = <<<SQL
            select noeud_arrivee_gid as noeud_routier_gid,troncon_gid,longueur, 
                   ST_X(noeud_depart_geom) as lat, ST_Y(noeud_depart_geom) as lon 
            from relation where noeud_depart_gid =:gidTag
            union
            select noeud_depart_gid as noeud_routier_gid,troncon_gid,longueur, 
                   ST_X(noeud_depart_geom) as lat, ST_Y(noeud_depart_geom) as lon 
            from relation where noeud_arrivee_gid =:gidTag
        SQL;
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($requeteSQL);
        $pdoStatement->execute(array(
            "gidTag" => $noeudRoutierGid
        ));
        return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getVoisins2(int $noeudRoutierGid): array
    {
        $requeteSQL = <<<SQL
            (select * from relation r where noeud_depart_gid = :gidTag or noeud_arrivee_gid = :gidTag);
        SQL;
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($requeteSQL);
        $pdoStatement->execute(array(
            "gidTag" => $noeudRoutierGid
        ));
        return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getShortestPathDijkstra(int $noeudRoutierDepartGid,int $noeudRoutierArriveeGid): array
    {
        $requeteSQL =
            "SELECT noeud_arrivee_gid as noeud_routier_gid,troncon_gid,r.longueur, ST_X(noeud_depart_geom) as lat, ST_Y(noeud_depart_geom) as lon, noeud_depart_gid, agg_cost as distance
            FROM pgr_dijkstra(
              'SELECT troncon_gid AS id,
                  noeud_arrivee_gid AS source, 
                  noeud_depart_gid AS target,
                  longueur AS cost
                FROM relation',
              ".$noeudRoutierDepartGid .", " . $noeudRoutierArriveeGid . ",
              directed => false
            ) AS a
            JOIN relation AS r ON (a.edge = r.troncon_gid)";
        $pdoStatement = ConnexionBaseDeDonnees::getPdo()->prepare($requeteSQL);
        $pdoStatement->execute();
        return $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getShortestPathAstar(int $noeudRoutierDepartGid,int $noeudRoutierArriveeGid): array
    {
        $requeteSQL =
            "SELECT noeud_arrivee_gid as noeud_routier_gid,edge as troncon_gid,cost as longueur, ST_X(noeud_depart_geom) as lat, ST_Y(noeud_depart_geom) as lon, node as noeud_depart_gid, agg_cost as distance
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
