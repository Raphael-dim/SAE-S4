<?php

namespace App\PlusCourtChemin\Modele\DataObject;

class NoeudCommune extends AbstractDataObject
{
    public function __construct(
        private int    $gid,
        private string $id_rte500,
        private string $nomCommune,
        private string $numInsee,
        private string $id_nd_rte,
        private string $long,
        private string $lat
    )
    {
    }

    public function getGid(): int
    {
        return $this->gid;
    }

    public function getId_rte500(): string
    {
        return $this->id_rte500;
    }

    public function getId_nd_rte(): string
    {
        return $this->id_nd_rte;
    }

    public function getNomCommune(): string
    {
        return $this->nomCommune;
    }

    public function getNumInsee(): string
    {
        return $this->numInsee;
    }

    public function getLongCommune(): string
    {
        return $this->long;
    }

    public function getLatCommune(): string
    {
        return $this->lat;
    }

    public function exporterEnFormatRequetePreparee(): array
    {
        // Inutile car on ne fait pas d'ajout ni de mise-à-jour
        return [];
    }

    public function trie($other): int
    {
        return $this . $this->nomCommune < $other . $this->nomCommune;
    }

    public function jsonSerialize(): array
    {
        return [
            "gid" => $this->gid,
            "id_rte500" => $this->id_rte500,
            "nomCommune" => $this->nomCommune,
            "numInsee" => $this->numInsee,
            "id_nd_rte" => $this->id_nd_rte,
            "long" => $this->long,
            "lat" => $this->lat,
        ];
    }
}
