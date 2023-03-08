<?php

namespace App\PlusCourtChemin\Modele\Repository;

use App\PlusCourtChemin\Modele\DataObject\AbstractDataObject;
use App\PlusCourtChemin\Modele\DataObject\NoeudCommune;
use PDO;

class NoeudCommuneRepository extends AbstractRepository
{

    public function construireDepuisTableau(array $noeudRoutierTableau): NoeudCommune
    {
        return new NoeudCommune(
            $noeudRoutierTableau["gid"],
            $noeudRoutierTableau["id_rte500"],
            $noeudRoutierTableau["nom_comm"],
            $noeudRoutierTableau["id_nd_rte"],
            $noeudRoutierTableau["st_x"],
            $noeudRoutierTableau["st_y"],
        );
    }

    protected function getNomTable(): string
    {
        return 'noeud_commune';
    }

    protected function getNomClePrimaire(): string
    {
        return 'gid';
    }

    protected function getNomsColonnes(): array
    {
        return ["gid", "id_rte500", "nom_comm", "id_nd_rte", " ST_X(geom)", "ST_Y(geom)"];
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

    public function recuperer($start = 0, $limit = 200, $nomCommune = null): array
    {
        $nomTable = $this->getNomTable();
        $champsSelect = implode(", ", $this->getNomsColonnes());
        if (is_null($nomCommune)) {
            $requeteSQL = <<<SQL
            SELECT $champsSelect FROM $nomTable LIMIT $limit OFFSET $start;
            SQL;
            $pdoStatement = ConnexionBaseDeDonnees::getPdo()->query($requeteSQL);
        } else {
            $requeteSQL = <<<SQL
            SELECT $champsSelect FROM $nomTable WHERE nom_comm LIKE :nameTag LIMIT limit OFFSET $start;
            SQL;
            $req_prep = ConnexionBaseDeDonnees::getPdo()->prepare($requeteSQL);
            $values = array("name_tag" => $nomCommune . "%");
            $req_prep->execute($values);
            $req_prep->setFetchMode(PDO::FETCH_OBJ);
            $pdoStatement = $req_prep->fetchAll();
        }

        $objets = [];
        foreach ($pdoStatement as $objetFormatTableau) {
            $objets[] = $this->construireDepuisTableau($objetFormatTableau);
        }

        return $objets;
    }
}
