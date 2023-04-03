<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Lib\dijkstra;
use App\PlusCourtChemin\Lib\MessageFlash;
use App\PlusCourtChemin\Lib\PlusCourtChemin;
use App\PlusCourtChemin\Modele\DataObject\Trajet;
use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;
use App\PlusCourtChemin\Modele\Repository\NoeudRoutierRepository;
use App\PlusCourtChemin\Modele\Repository\TrajetRepository;
use App\PlusCourtChemin\Modele\Repository\TronconRouteRepository;
use Symfony\Component\HttpFoundation\Response;

class ControleurNoeudCommune extends ControleurGenerique
{

    public static function afficherListe(): Response
    {

        if (!isset($_POST["start"]) || $_POST["start"] < 0) {
            $start = 0;
        } else {
            $start = $_POST["start"];
        }
        $limit = 200;
        if (isset($_POST["keyword"])) {
            $keyword = $_POST["keyword"];
            $noeudsCommunes = (new NoeudCommuneRepository())->recupererWhere(["nom_comm" => "LIKE LOWER('" . $keyword . "%')"], $limit);     //appel au modèle pour gerer la BD
        } else {
            $noeudsCommunes = (new NoeudCommuneRepository())->recuperer($start, $limit);     //appel au modèle pour gerer la BD
        }
        return ControleurNoeudCommune::afficherVue('vueGenerale.php', [
            "noeudsCommunes" => $noeudsCommunes,
            "pagetitle" => "Liste des Noeuds Routiers",
            "cheminVueBody" => "noeudCommune/liste.php",
            "limit" => $limit,
            "start" => $start
        ]);
    }

    public static function afficherDetail($idCommune): Response
    {
        if (!isset($idCommune)) {
            MessageFlash::ajouter("danger", "Immatriculation manquante.");
            return ControleurNoeudCommune::rediriger("/");
        }

        $noeudCommune = (new NoeudCommuneRepository())->recupererParClePrimaire($idCommune);

        if ($noeudCommune === null) {
            MessageFlash::ajouter("warning", "gid inconnue.");
            return ControleurNoeudCommune::rediriger("/");
        }

        return ControleurNoeudCommune::afficherVue('vueGenerale.php', [
            "noeudCommune" => $noeudCommune,
            "pagetitle" => "Détail de la noeudCommune",
            "cheminVueBody" => "noeudCommune/detail.php"
        ]);
    }

    public static function plusCourtChemin(): Response
    {
        $parametres = [
            "pagetitle" => "Plus court chemin",
            "cheminVueBody" => "noeudCommune/plusCourtChemin.php",
        ];


        if (!empty($_POST)) {
            $t1 = time();
            $nomsCommune = $_POST["nomsCommune"];

            $noeudCommuneRepository = new NoeudCommuneRepository();
            //            /** @var NoeudCommune $noeudCommuneDepart */
            //
            //
            foreach($nomsCommune as $nom){
                $noeudsCommune[] = $noeudCommuneRepository->recupererPar(["nom_comm" => $nom])[0];
            }
            $noeudRoutierRepository = new NoeudRoutierRepository();
            foreach($noeudsCommune as $noeud){
                $noeudsRoutier[] = $noeudRoutierRepository->recupererPar(["id_rte500" => $noeud->getId_nd_rte()])[0];
            }


            for($i = 0;$i<count($noeudsRoutier)-1;$i++){
                $results[] = (new NoeudRoutierRepository())->getShortestPathAstar($noeudsRoutier[$i]->getGid(), $noeudsRoutier[$i+1]->getGid());
            }

            if (ConnexionUtilisateur::estConnecte()) {
                $trajet = new Trajet(ConnexionUtilisateur::getLoginUtilisateurConnecte(), $noeudsCommune[0]->getGid(),
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
            $t2 = time();
            $parametres["temps"] = $t2 - $t1;
            MessageFlash::ajouter('success', 'Temps d\'execution: '.$parametres["temps"]." s");

        }

        return ControleurNoeudCommune::afficherVue('vueGenerale.php', $parametres);
    }
}