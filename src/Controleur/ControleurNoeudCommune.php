<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Lib\dijkstra;
use App\PlusCourtChemin\Lib\MessageFlash;
use App\PlusCourtChemin\Lib\PlusCourtChemin;
use App\PlusCourtChemin\Lib\Route;
use App\PlusCourtChemin\Modele\DataObject\NoeudCommune;
use App\PlusCourtChemin\Modele\DataObject\Trajet;
use App\PlusCourtChemin\Modele\DataObject\Voisin;
use App\PlusCourtChemin\Modele\Repository\ConnexionBaseDeDonnees;
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

            $latDepart = $noeudCommuneDepart->getLatCommune();
            $longDepart = $noeudCommuneDepart->getLongCommune();
            $latArrivee = $noeudCommuneArrivee->getLatCommune();
            $longArrivee = $noeudCommuneArrivee->getLongCommune();

            $sql = "SELECT *
            FROM relation r
                WHERE ST_Contains(
                ST_MakeEnvelope($latDepart, $longDepart, $latArrivee, $longArrivee, 4326),
                noeud_depart_geom);
             ";
            $pdoStatement = ConnexionBaseDeDonnees::getPdo()->query($sql);
            $voisins = [];
            foreach ($pdoStatement as $objetFormatTableau) {
                $troncon_gid = $objetFormatTableau['troncon_gid'];
                $noeud_depart_gid = $objetFormatTableau['noeud_depart_gid'];
                $noeud_depart_geom = $objetFormatTableau['noeud_depart_geom'];
                $noeud_arrivee_gid = $objetFormatTableau['noeud_arrivee_gid'];
                $noeud_arrivee_geom = $objetFormatTableau['noeud_arrivee_geom'];
                $longueur = $objetFormatTableau['longueur'];
                $voisins[$noeud_depart_gid][$noeud_arrivee_gid] = new Voisin($troncon_gid, $noeud_depart_gid,
                            $noeud_depart_geom, $noeud_arrivee_gid, $noeud_arrivee_geom, $longueur);
            }

            $plusCourtChemin = new PlusCourtChemin($noeudRoutierDepart->getGid(), $noeudCommuneArrivee->getGid(), $voisins);
            $plusCourtChemin->calculer();


            $troncons = [];

            foreach (array_column($result, 'troncon_gid') as $troncon) {
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
