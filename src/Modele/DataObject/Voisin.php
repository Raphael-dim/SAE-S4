<?php

namespace App\PlusCourtChemin\Modele\DataObject;

class Voisin
{
    private int $troncon_gid;
    private int $noeud_depart_gid;
    private string $noeud_depart_geom;
    private int $noeud_arrivee_gid;
    private string $noeud_arrivee_geom;
    private int $longueur;

    /**
     * @param int $troncon_gid
     * @param int $noeud_depart_gid
     * @param string $noeud_depart_geom
     * @param int $noeud_arrivee_gid
     * @param string $noeud_arrivee_geom
     * @param int $longueur
     */
    public function __construct(int $troncon_gid, int $noeud_depart_gid, string $noeud_depart_geom, int $noeud_arrivee_gid, string $noeud_arrivee_geom, int $longueur)
    {
        $this->troncon_gid = $troncon_gid;
        $this->noeud_depart_gid = $noeud_depart_gid;
        $this->noeud_depart_geom = $noeud_depart_geom;
        $this->noeud_arrivee_gid = $noeud_arrivee_gid;
        $this->noeud_arrivee_geom = $noeud_arrivee_geom;
        $this->longueur = $longueur;
    }

    /**
     * @return int
     */
    public function getTronconGid(): int
    {
        return $this->troncon_gid;
    }

    /**
     * @param int $troncon_gid
     */
    public function setTronconGid(int $troncon_gid): void
    {
        $this->troncon_gid = $troncon_gid;
    }

    /**
     * @return int
     */
    public function getNoeudDepartGid(): int
    {
        return $this->noeud_depart_gid;
    }

    /**
     * @param int $noeud_depart_gid
     */
    public function setNoeudDepartGid(int $noeud_depart_gid): void
    {
        $this->noeud_depart_gid = $noeud_depart_gid;
    }

    /**
     * @return string
     */
    public function getNoeudDepartGeom(): string
    {
        return $this->noeud_depart_geom;
    }

    /**
     * @param string $noeud_depart_geom
     */
    public function setNoeudDepartGeom(string $noeud_depart_geom): void
    {
        $this->noeud_depart_geom = $noeud_depart_geom;
    }

    /**
     * @return int
     */
    public function getNoeudArriveeGid(): int
    {
        return $this->noeud_arrivee_gid;
    }

    /**
     * @param int $noeud_arrivee_gid
     */
    public function setNoeudArriveeGid(int $noeud_arrivee_gid): void
    {
        $this->noeud_arrivee_gid = $noeud_arrivee_gid;
    }

    /**
     * @return string
     */
    public function getNoeudArriveeGeom(): string
    {
        return $this->noeud_arrivee_geom;
    }

    /**
     * @param string $noeud_arrivee_geom
     */
    public function setNoeudArriveeGeom(string $noeud_arrivee_geom): void
    {
        $this->noeud_arrivee_geom = $noeud_arrivee_geom;
    }

    /**
     * @return int
     */
    public function getLongueur(): int
    {
        return $this->longueur;
    }

    /**
     * @param int $longueur
     */
    public function setLongueur(int $longueur): void
    {
        $this->longueur = $longueur;
    }
}