<?php

namespace App\PlusCourtChemin\Modele\DataObject;


use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;

class Trajet extends AbstractDataObject
{

    private string $loginUtilisateur;
    private NoeudCommune $commune_depart;
    private NoeudCommune $commune_arrivee;
    private int $id;
    private string $date;

    public function __construct(string $loginUtilisateur, string $gid_commune_depart,
                                string $gid_commune_arrivee,
                                string $date)
    {
        $this->loginUtilisateur = $loginUtilisateur;
        $this->commune_depart = (new NoeudCommuneRepository())->recupererParClePrimaire($gid_commune_depart);
        $this->commune_arrivee = (new NoeudCommuneRepository())->recupererParClePrimaire($gid_commune_arrivee);
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }


    /**
     * @return string
     */
    public function getLoginUtilisateur(): string
    {
        return $this->loginUtilisateur;
    }

    /**
     * @param string $loginUtilisateur
     */
    public function setLoginUtilisateur(string $loginUtilisateur): void
    {
        $this->loginUtilisateur = $loginUtilisateur;
    }

    /**
     * @return int
     */
    public function getGidCommuneDepart(): int
    {
        return $this->gid_commune_depart;
    }

    /**
     * @param int $gid_commune_depart
     */
    public function setGidCommuneDepart(int $gid_commune_depart): void
    {
        $this->gid_commune_depart = $gid_commune_depart;
    }

    /**
     * @return Commune
     */
    public function getCommuneDepart(): NoeudCommune
    {
        return $this->commune_depart;
    }

    /**
     * @param Commune $commune_depart
     */
    public function setCommuneDepart(Commune $commune_depart): void
    {
        $this->commune_depart = $commune_depart;
    }

    /**
     * @return Commune
     */
    public function getCommuneArrivee(): NoeudCommune
    {
        return $this->commune_arrivee;
    }

    /**
     * @param Commune $commune_arrivee
     */
    public function setCommuneArrivee(Commune $commune_arrivee): void
    {
        $this->commune_arrivee = $commune_arrivee;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }


    public function exporterEnFormatRequetePreparee(): array
    {
        return array(
            "gid_commune_depart_tag" => $this->commune_depart->getGid(),
            "gid_commune_arrivee_tag" => $this->commune_arrivee->getGid(),
            "loginutilisateur_tag" => $this->loginUtilisateur,
            "date_tag" => $this->date,
            "idtrajet_tag" => 0
        );
    }
}