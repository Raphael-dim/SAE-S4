<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Lib\dijkstra;
use App\PlusCourtChemin\Lib\MessageFlash;
use App\PlusCourtChemin\Lib\PlusCourtChemin;
use App\PlusCourtChemin\Lib\Route;
use App\PlusCourtChemin\Modele\DataObject\NoeudCommune;
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
            $nomCommuneDepart = $_POST["nomCommuneDepart"];
            $nomCommuneArrivee = $_POST["nomCommuneArrivee"];

            $noeudCommuneRepository = new NoeudCommuneRepository();
            //            /** @var NoeudCommune $noeudCommuneDepart */
            //
            //
            $noeudCommuneDepart = $noeudCommuneRepository->recupererPar(["nom_comm" => $nomCommuneDepart])[0];
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

            if (ConnexionUtilisateur::estConnecte()) {
                $trajet = new Trajet(ConnexionUtilisateur::getLoginUtilisateurConnecte(), $noeudCommuneDepart->getGid(),
                    $noeudCommuneArrivee->getGid(), date('Y-m-d : H:i:s'));
                (new TrajetRepository())->ajouter($trajet);
            }
            $pcc = new PlusCourtChemin($noeudRoutierDepart->getGid(), $noeudRoutierArrivee->getGid());
            $pcc->setDistanceInitiale($noeudRoutierDepart->getLatNoeud(), $noeudRoutierDepart->getLongNoeud(),
                $noeudRoutierArrivee->getLatNoeud(), $noeudRoutierArrivee->getLongNoeud());
            $result = $pcc->calculer();
            //$result = (new NoeudRoutierRepository())->getShortestPathAstar($noeudRoutierDepart->getGid(), $noeudRoutierArrivee->getGid());

            $plusCourtChemin = Route::getShortestPath($result, $noeudRoutierDepart->getGid(), $noeudRoutierArrivee->getGid());

            $distance = $plusCourtChemin["distance"];
            //$distance = number_format(end($result)["distance"],2);

            $troncons = [];

            foreach($plusCourtChemin["path"] as $troncon){
            //foreach (array_column($result, 'troncon_gid') as $troncon) {
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
            $t2 = time();
            $parametres["temps"] = $t2 - $t1;

        }

        return ControleurNoeudCommune::afficherVue('vueGenerale.php', $parametres);
    }
}
