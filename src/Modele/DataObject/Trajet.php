<?php

namespace App\PlusCourtChemin\Modele\DataObject;


use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;

class Trajet extends AbstractDataObject
{

    private string $loginUtilisateur;
    private string $gid_commune_depart;
    private string $gid_commune_arrivee;
    private int $id;
    private string $date;

    public function __construct(string $loginUtilisateur, string $gid_commune_depart,
                                string $gid_commune_arrivee,
                                string $date)
    {
        $this->loginUtilisateur = $loginUtilisateur;
        $this->gid_commune_depart = $gid_commune_depart;
        $this->gid_commune_arrivee = $gid_commune_arrivee;
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
     * @return int
     */
    public function getGidCommuneArrivee(): int
    {
        return $this->gid_commune_arrivee;
    }

    /**
     * @param int $gid_commune_arrivee
     */
    public function setGidCommuneArrivee(int $gid_commune_arrivee): void
    {
        $this->gid_commune_arrivee = $gid_commune_arrivee;
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

    public function getCommuneDepart(): NoeudCommune
    {
        return (new NoeudCommuneRepository())->recupererParClePrimaire($this->gid_commune_depart);
    }

    public function getCommuneArrivee(): NoeudCommune
    {
        return (new NoeudCommuneRepository())->recupererParClePrimaire($this->gid_commune_arrivee);
    }


    public function exporterEnFormatRequetePreparee(): array
    {
        return array(
            "gid_commune_depart_tag" => $this->gid_commune_depart,
            "gid_commune_arrivee_tag" => $this->gid_commune_arrivee,
            "loginutilisateur_tag" => $this->loginUtilisateur,
            "date_tag" => $this->date
        );
    }
}