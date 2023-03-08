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
        private string $long_start,
        private string $lat_start,
        private string $long_end,
        private string $lat_end
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

    public function getLatStart():string{
        return $this->lat_start;
    }

    public function  getLatEnd():string{
        return $this->lat_end;
    }

    public function getLongStart():string{
        return $this->long_start;
    }

    public function  getLongEnd():string{
        return $this->long_end;
    }

    public function exporterEnFormatRequetePreparee(): array
    {
        // Inutile car pas d'ajout ni de màj
        return [];
    }
}
