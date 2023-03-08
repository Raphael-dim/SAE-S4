<?php

namespace App\PlusCourtChemin\Modele\DataObject;


class TronconRoute extends AbstractDataObject
{

    public function __construct(
        private int $gid,
        private string $id_rte500,
        private string $sens,
        private string $numeroRoute,
        private float $longueur,
        private string $geom,
    ) {
    }

    public function getGid(): int
    {
        return $this->gid;
    }

    public function getId_rte500(): string
    {
        return $this->id_rte500;
    }

    public function getSens(): string
    {
        return $this->sens;
    }

    public function getNumeroRoute(): string
    {
        return $this->numeroRoute;
    }

    public function getLongueur(): float
    {
        return $this->longueur;
    }

    public function getGeom():string{
        return $this->geom;
    }


    public function exporterEnFormatRequetePreparee(): array
    {
        // Inutile car pas d'ajout ni de màj
        return [];
    }

    public function jsonSerialize() : array
    {
        return [
            "gid" => $this->gid,
            "id_rte500" => $this->id_rte500,
            "sens" => $this->sens,
            "numeroRoute" =>  $this->numeroRoute,
            "longueur" => $this->longueur,
            "geom" => json_decode($this->geom),
        ];
    }
}
