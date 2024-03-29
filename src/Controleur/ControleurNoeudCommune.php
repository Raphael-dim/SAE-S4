<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Configuration\Configuration;
use App\PlusCourtChemin\Lib\MessageFlash;
use App\PlusCourtChemin\Service\Exception\ServiceException;
use App\PlusCourtChemin\Service\NoeudCommuneService;
use Symfony\Component\HttpFoundation\Response;

class ControleurNoeudCommune extends ControleurGenerique
{

    public static function afficherListe(): Response
    {
        $limit = 200;
        $keyword = $_POST["keyword"] ?? null;
        $start = $_POST["start"] ?? null;
        try {
            $noeudsCommunes = NoeudCommuneService::recupererCommunes($keyword);
        }
        catch(ServiceException $e) {
            MessageFlash::ajouter("error", $e->getMessage());
        }

        /*return ControleurNoeudCommune::afficherVue('vueGenerale.php', [
            "noeudsCommunes" => $noeudsCommunes,
            "pagetitle" => "Liste des Noeuds Routiers",
            "cheminVueBody" => "noeudCommune/liste.php",
            "limit" => $limit,
            "start" => $start
        ]);*/

        return ControleurUtilisateur::afficherTwig('noeudCommune/liste.html.twig',[
            "page_title" => "Liste des Noeuds Routiers",
            "method" => Configuration::getDebug() ? "get" : "post",
            "limit" => $limit,
            "start" => $start,
            "noeudsCommunes" => $noeudsCommunes
        ]);
    }

    public static function afficherDetail($idCommune): Response
    {
        try{
            $noeudCommune = NoeudCommuneService::recupererDetailCommune($idCommune);
        }catch(ServiceException $e){
            MessageFlash::ajouter("warning", $e->getMessage());
            return ControleurNoeudCommune::rediriger("communes");
        }

        /*return ControleurNoeudCommune::afficherVue('vueGenerale.php', [
            "noeudCommune" => $noeudCommune,
            "pagetitle" => "Détail de la commune",
            "cheminVueBody" => "noeudCommune/detail.php"
        ]);*/

        return ControleurUtilisateur::afficherTwig('noeudCommune/detail.html.twig',[
            "page_title" => "Détail de la commune",
            "method" => Configuration::getDebug() ? "get" : "post",
            "noeudCommune" => $noeudCommune
        ]);
    }

    public static function plusCourtChemin(): Response
    {
        $parametres = [
            "pagetitle" => "Plus court chemin",
            "cheminVueBody" => "noeudCommune/plusCourtChemin.php"
        ];
        $plusCourtChemin = [];

        if (!empty($_POST)) {
            try{
                $plusCourtChemin = NoeudCommuneService::calculPlusCourtChemin($_POST["nomsCommune"]);
            }catch(ServiceException $e){
                MessageFlash::ajouter('danger', $e->getMessage());
                return ControleurNoeudCommune::rediriger("plusCourtChemin");
            }
            MessageFlash::ajouter('success', 'Temps d\'execution: '.$plusCourtChemin["temps"]." s");
        }

        return ControleurNoeudCommune::afficherVue('vueGenerale.php', array_merge($parametres,$plusCourtChemin));
    }
}