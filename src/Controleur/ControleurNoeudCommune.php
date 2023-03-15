<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Lib\dijkstra;
use App\PlusCourtChemin\Lib\MessageFlash;
use App\PlusCourtChemin\Lib\PlusCourtChemin;
use App\PlusCourtChemin\Modele\DataObject\NoeudCommune;
use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;
use App\PlusCourtChemin\Modele\Repository\NoeudRoutierRepository;
use App\PlusCourtChemin\Modele\Repository\TronconRouteRepository;

class ControleurNoeudCommune extends ControleurGenerique
{

    public static function afficherErreur($errorMessage = "", $controleur = ""): void
    {
        parent::afficherErreur($errorMessage, "noeudCommune");
    }

    public static function afficherListe(): void
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
        ControleurNoeudCommune::afficherVue('vueGenerale.php', [
            "noeudsCommunes" => $noeudsCommunes,
            "pagetitle" => "Liste des Noeuds Routiers",
            "cheminVueBody" => "noeudCommune/liste.php",
            "limit" => $limit,
            "start" => $start
        ]);
    }

    public static function afficherDetail($idCommune): void
    {
        if (!isset($idCommune)) {
            MessageFlash::ajouter("danger", "Immatriculation manquante.");
            ControleurNoeudCommune::rediriger("/");
        }

        $noeudCommune = (new NoeudCommuneRepository())->recupererParClePrimaire($idCommune);

        if ($noeudCommune === null) {
            MessageFlash::ajouter("warning", "gid inconnue.");
            ControleurNoeudCommune::rediriger("/");
        }

        ControleurNoeudCommune::afficherVue('vueGenerale.php', [
            "noeudCommune" => $noeudCommune,
            "pagetitle" => "Détail de la noeudCommune",
            "cheminVueBody" => "noeudCommune/detail.php"
        ]);
    }

    public static function plusCourtChemin(): void
    {
        $parametres = [
            "pagetitle" => "Plus court chemin",
            "cheminVueBody" => "noeudCommune/plusCourtChemin.php",
        ];


        if (!empty($_POST)) {
            $nomCommuneDepart = $_POST["nomCommuneDepart"];
            $nomCommuneArrivee = $_POST["nomCommuneArrivee"];

            $noeudCommuneRepository = new NoeudCommuneRepository();
            //            /** @var NoeudCommune $noeudCommuneDepart */
            //
            //
            $noeudCommuneDepart = $noeudCommuneRepository->recupererPar(["nom_comm" => $nomCommuneDepart])[0];
            //            /** @var NoeudCommune $noeudCommuneArrivee */
            $noeudCommuneArrivee = $noeudCommuneRepository->recupererPar(["nom_comm" => $nomCommuneArrivee])[0];
            //
            $noeudRoutierRepository = new NoeudRoutierRepository();
            echo "<br>";

            $noeudRoutierDepart = $noeudRoutierRepository->recupererPar([
                "id_rte500" => $noeudCommuneDepart->getId_nd_rte()
            ])[0];


            $noeudRoutierArrivee = $noeudRoutierRepository->recupererPar([
                "id_rte500" => $noeudCommuneArrivee->getId_nd_rte()
            ])[0];

            $pcc = new PlusCourtChemin($noeudRoutierDepart->getGid(), $noeudRoutierArrivee->getGid());
            $pcc->setDistanceInitiale($noeudRoutierDepart->getLatNoeud(), $noeudRoutierDepart->getLongNoeud(),
                $noeudRoutierArrivee->getLatNoeud(), $noeudRoutierArrivee->getLongNoeud());
            $result = $pcc->calculer();
            $distance = 0 ;

            $troncons_route = [];


            foreach ($result as $n){
                $distance += $n['distance'];
                if (isset($n['troncon_gid'])) {
                    $troncons_route[] = $n['troncon_gid'];
                }
            }

            $troncons = [];

            foreach ($troncons_route as $troncon) {
                $troncons[] = (new TronconRouteRepository())->recupererParClePrimaire($troncon);
            }

            $parametres["CommuneDepart"] = $noeudCommuneDepart;
            $parametres["CommuneArrivee"] = $noeudCommuneArrivee;
            $parametres["noeudDepart"] = $noeudRoutierRepository->recupererPar([
                "id_rte500" => $noeudCommuneDepart->getId_nd_rte()
            ])[0];
            $parametres["noeudArrivee"] = $noeudRoutierArriveeGid = $noeudRoutierRepository->recupererPar([
                "id_rte500" => $noeudCommuneArrivee->getId_nd_rte()
            ])[0];
            $parametres["distance"] = $distance;
            $parametres["troncons"] = $troncons;
        }

        ControleurNoeudCommune::afficherVue('vueGenerale.php', $parametres);
    }
}
