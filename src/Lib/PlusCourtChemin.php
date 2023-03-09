<?php

namespace App\PlusCourtChemin\Lib;

use App\PlusCourtChemin\Modele\DataObject\NoeudRoutier;
use App\PlusCourtChemin\Modele\Repository\NoeudRoutierRepository;
use SplPriorityQueue;

class PlusCourtChemin
{
    private array $chemin;
    private array $noeudsALaFrontiere;

    public function __construct(
        private int $noeudRoutierDepartGid,
        private int $noeudRoutierArriveeGid
    ) {
    }

    public function calculer(bool $affichageDebug = false): array
    {

        $noeudRoutierRepository = new NoeudRoutierRepository();

        // Distance en km, table indexï¿½ par NoeudRoutier::gid
        $this->chemin = [$this->noeudRoutierDepartGid => ["distance" => 0,"pred" => -1]];
        $priorityQdist = new PriorityQ();
        $priorityQdist->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
        $priorityQdist->insert($this->noeudRoutierDepartGid,0);


        while ($priorityQdist->count() !== 0) {
            $courant = $priorityQdist->extract();
            $noeudRoutierGidCourant = $courant["data"];

            // Fini
            if ($noeudRoutierGidCourant === $this->noeudRoutierArriveeGid) {
                return $this->chemin;
            }

            /** @var NoeudRoutier $noeudRoutierCourant */
            $noeudRoutierCourant = new NoeudRoutier($noeudRoutierGidCourant);
            $voisins = $noeudRoutierCourant->getVoisins();

            $i = 1;
            foreach ($voisins as $voisin) {
                $noeudVoisinGid = $voisin["noeud_routier_gid"];
                $distanceTroncon = $voisin["longueur"];
                $distanceProposee = $courant["priority"] + $distanceTroncon;
                if (!isset($this->chemin[$noeudVoisinGid]) || $distanceProposee < $this->chemin[$noeudVoisinGid]["distance"]) {
                    $this->chemin[$noeudVoisinGid]["distance"] = $distanceProposee;
                    $priorityQdist->insert($noeudVoisinGid,$distanceProposee);
                    $this->chemin[$noeudVoisinGid]["pred"] = $noeudRoutierGidCourant;
                    $this->chemin[$noeudVoisinGid]["troncon_gid"] = $voisin["troncon_gid"];
                }
            }


        }
    }

    private function noeudALaFrontiereDeDistanceMinimale()
    {
        $noeudRoutierDistanceMinimaleGid = -1;
        $distanceMinimale = PHP_INT_MAX;
        foreach ($this->noeudsALaFrontiere as $noeudRoutierGid => $valeur) {
            if ($this->chemin[$noeudRoutierGid] < $distanceMinimale) {
                $noeudRoutierDistanceMinimaleGid = $noeudRoutierGid;
                $distanceMinimale = $this->chemin[$noeudRoutierGid];
            }
        }
        return $noeudRoutierDistanceMinimaleGid;
    }




}