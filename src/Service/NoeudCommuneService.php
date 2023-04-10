<?php

namespace App\PlusCourtChemin\Service;

use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Modele\DataObject\AbstractDataObject;
use App\PlusCourtChemin\Modele\DataObject\Trajet;
use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;
use App\PlusCourtChemin\Modele\Repository\NoeudRoutierRepository;
use App\PlusCourtChemin\Modele\Repository\TrajetRepository;
use App\PlusCourtChemin\Modele\Repository\TronconRouteRepository;
use App\PlusCourtChemin\Service\Exception\ServiceException;

class NoeudCommuneService
{
    /**
     * @throws ServiceException
     */
    static function recupererCommunes($keyword, $limit = 200): array
    {
        if (!isset($_POST["start"]) || $_POST["start"] < 0) {
            $start = 0;
        } else {
            $start = $_POST["start"];
        }
        if (isset($keyword)) {
            $communes = (new NoeudCommuneRepository())->recupererWhere(["nom_comm" => "LIKE LOWER('" . $keyword . "%')"], $limit);     //appel au modèle pour gerer la BD
        } else {
            $communes = (new NoeudCommuneRepository())->recuperer($start, $limit);     //appel au modèle pour gerer la BD
        }
        if(empty($communes)){
            throw new ServiceException("Aucune communes");
        }
        return $communes;

    }

    /**
     * @throws ServiceException
     */
    static function recupererDetailCommune($idCommune): AbstractDataObject
    {
        if(!isset($idCommune)){
            throw new ServiceException("Immatriculation manquante.");
        }

        $noeudCommune = (new NoeudCommuneRepository())->recupererParClePrimaire($idCommune);

        if ($noeudCommune === null) {
            throw new ServiceException("Numéro gid inconnu.");
        }
        return $noeudCommune;
    }

    /**
     * @throws ServiceException
     */
    static function calculPlusCourtChemin($nomsCommune): array
    {
        $parametres = [
            "pagetitle" => "Plus court chemin",
            "cheminVueBody" => "noeudCommune/plusCourtChemin.php",
        ];

        $t1 = microtime(true);

        $noeudCommuneRepository = new NoeudCommuneRepository();
        $noeudRoutierRepository = new NoeudRoutierRepository();

        foreach($nomsCommune as $nom){
            $noeudsCommune[] = array_key_first($noeudCommuneRepository->recupererPar(["nom_comm" => $nom]));
            if(is_null(end($noeudsCommune)) || empty(end($noeudsCommune))){
                throw new ServiceException("La ville " . $nom . " n'existe pas");
            }
            if(array_count_values($nomsCommune)[$nom] >= 2){
                throw new ServiceException("La ville " . $nom . " ne pas pas apparaitre plusieurs fois");
            }
        }

        foreach($noeudsCommune as $noeud){
            $noeudsRoutier[] = array_key_first($noeudRoutierRepository->recupererPar(["id_rte500" => $noeud->getId_nd_rte()]));
            if(is_null(end($noeudsRoutier)) || empty(end($noeudsRoutier))){
                throw new ServiceException("Le numéro de route " . $noeud->getId_nd_rte() . " n'existe pas");
            }
            if(count(array_filter($noeudsCommune, function($v) {return str_contains($v->getNumInsee(),"2A") || str_contains($v->getNumInsee(),"2B");})) != count($noeudsCommune)
            && count(array_filter($noeudsCommune, function($v) {return str_contains($v->getNumInsee(),"2A") || str_contains($v->getNumInsee(),"2B");})) != 0){
                throw new ServiceException("Vous ne pouvez pas tracer de chemin entre la France et la Corse");
            }
        }

        /*echo "<br>";*/

        for($i = 0;$i<count($noeudsRoutier)-1;$i++){
            $results[] = (new NoeudRoutierRepository())->getShortestPathAstar($noeudsRoutier[$i]->getGid(), $noeudsRoutier[$i+1]->getGid());
            if(is_null(end($results)) || empty(end($results))){
                throw new ServiceException("Le chemin est vide");
            }
        }

        if (ConnexionUtilisateur::estConnecte()) {
            $trajet = new Trajet(ConnexionUtilisateur::getLoginUtilisateurConnecte(), array_key_first($noeudsCommune)->getGid(),
                end($noeudsCommune)->getGid(), date('Y-m-d : H:i:s'));
            (new TrajetRepository())->ajouter($trajet);
        }

        $distance = 0;
        foreach($results as $result){
            $distance = $distance + end($result)["distance"];
        }
        $distance = number_format($distance,3);
        $troncons = [];

        foreach($results as $result){
            foreach (array_column($result, 'troncon_gid') as $troncon) {
                $troncons[] = (new TronconRouteRepository())->recupererParClePrimaire($troncon);
            }
        }

        $parametres["Communes"] = $noeudsCommune;
        $parametres["noeuds"] = $noeudsRoutier;
        $parametres["distance"] = $distance;
        $parametres["troncons"] = $troncons;
        $t2 = microtime(true);
        $parametres["temps"] = round($t2 - $t1,2);
        return $parametres;
    }
}