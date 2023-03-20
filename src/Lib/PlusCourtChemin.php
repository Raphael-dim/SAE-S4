<?php

namespace App\PlusCourtChemin\Lib;

use App\PlusCourtChemin\Modele\DataObject\NoeudRoutier;
use App\PlusCourtChemin\Modele\Repository\NoeudRoutierRepository;
use SplPriorityQueue;

class PlusCourtChemin
{
    private array $chemin;
    private array $noeudsALaFrontiere;
    private float $latArrivee;
    private float $lonArrivee;
    private float $distanceInitiale;
    private int $noeudRoutierGidCourant;
    private array $test;

    public function __construct(
        private int $noeudRoutierDepartGid,
        private int $noeudRoutierArriveeGid
    )
    {

    }

    public function calculer(bool $affichageDebug = false): array
    {
        //var_dump($this->test);
        // Distance en km, table indexï¿½ par NoeudRoutier::gid
        $this->chemin = [$this->noeudRoutierDepartGid => ["distance" => 0, "pred" => -1]];
        $priorityQdist = new PriorityQ();
        $priorityQdist->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
        $priorityQdist->insert($this->noeudRoutierDepartGid, 0);
        //var_dump($this->test);

        while ($priorityQdist->count() !== 0) {
            $courant = $priorityQdist->extract();
            $this->noeudRoutierGidCourant = $courant["data"];

            //var_dump($this->noeudRoutierArriveeGid);
            // Fini
            if ($this->noeudRoutierGidCourant === $this->noeudRoutierArriveeGid) {
                return $this->chemin;
            }

            //$noeudRoutierCourant = new NoeudRoutier($this->noeudRoutierGidCourant);
            //$voisins = $noeudRoutierCourant->getVoisins();
            //var_dump($this->noeudRoutierGidCourant);
            $voisins = array_filter(
                $this->test,
                function ($var) {
                    return $var['noeud_routier_gid'] === $this->noeudRoutierGidCourant || $var['noeud_depart_gid'] === $this->noeudRoutierGidCourant;
                }
            );
            //echo 'latArrivee : ' .$this->latArrivee . 'longArrivee ; ' . $this->lonArrivee;

            $i = 1;
            foreach ($voisins as $voisin) {
                //echo ' lat : ' . $voisin['lat'] . ' lon : ' . $voisin['lon'];
                //echo 'distanceIni : ' . $this->distanceInitiale;
                //echo(' distance : ' . $this->distance($voisin['lat'], $voisin['lon'], $this->latArrivee, $this->lonArrivee));
                if($voisin["noeud_routier_gid"] === $this->noeudRoutierGidCourant){
                    $noeudVoisinGid = $voisin["noeud_depart_gid"];
                }else{
                    $noeudVoisinGid = $voisin["noeud_routier_gid"];
                }
                $distanceTroncon = $voisin["longueur"];
                $distanceProposee = $courant["priority"] + $distanceTroncon;
                if ($this->distance($voisin['lat'], $voisin['lon'], $this->latArrivee, $this->lonArrivee) < $this->distanceInitiale + 500 &&
                    (!isset($this->chemin[$noeudVoisinGid]) || $distanceProposee < $this->chemin[$noeudVoisinGid]["distance"])) {
                    $this->chemin[$noeudVoisinGid]["distance"] = $distanceProposee;
                    $priorityQdist->insert($noeudVoisinGid, $distanceProposee);
                    $this->chemin[$noeudVoisinGid]["pred"] = $this->noeudRoutierGidCourant;
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

    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $km = $dist * 60 * 1.1515 * 1.609344;
        return $km * 1000;
    }

    public function setDistanceInitiale($lat1, $lon1, $lat2, $lon2)
    {
        /*var_dump($lon1);
        var_dump($lat1);
        var_dump($lon2);
        var_dump($lat2);*/
        $this->test = (new NoeudRoutierRepository())->getLine($lon1, $lat1, $lon2, $lat2);
        $this->distanceInitiale = $this->distance($lat1, $lon1, $lat2, $lon2);
        /*var_dump($this->test);*/
        $this->latArrivee = $lat2;
        $this->lonArrivee = $lon2;
    }
}