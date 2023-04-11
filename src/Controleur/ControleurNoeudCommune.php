<?php

namespace App\PlusCourtChemin\Controleur;

use App\PlusCourtChemin\Lib\MessageFlash;
use App\PlusCourtChemin\Service\Exception\ServiceException;
use App\PlusCourtChemin\Service\NoeudCommuneService;
use Symfony\Component\HttpFoundation\Response;

class ControleurNoeudCommune extends ControleurGenerique
{

    /**
     * Affiche la liste des noeuds communes.
     *
     * @return Response La réponse HTTP.
     */
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

        return ControleurNoeudCommune::afficherVue('vueGenerale.php', [
            "noeudsCommunes" => $noeudsCommunes,
            "pagetitle" => "Liste des Noeuds Routiers",
            "cheminVueBody" => "noeudCommune/liste.php",
            "limit" => $limit,
            "start" => $start
        ]);
    }


    /**
     * Affiche le détail d'un noeud commune.
     *
     * @param string $idCommune L'identifiant du noeud commune.
     * @return Response La réponse HTTP.
     */
    public static function afficherDetail($idCommune): Response
    {
        try{
            $noeudCommune = NoeudCommuneService::recupererDetailCommune($idCommune);
        }catch(ServiceException $e){
            MessageFlash::ajouter("warning", $e->getMessage());
            return ControleurNoeudCommune::rediriger("communes");
        }

        return ControleurNoeudCommune::afficherVue('vueGenerale.php', [
            "noeudCommune" => $noeudCommune,
            "pagetitle" => "Détail de la commune",
            "cheminVueBody" => "noeudCommune/detail.php"
        ]);
    }

    public static function plusCourtChemin(): Response
    {
        $plusCourtChemin = [
            "pagetitle" => "Plus court chemin",
            "cheminVueBody" => "noeudCommune/plusCourtChemin.php",
        ];

        if (!empty($_POST)) {
            try{
                $plusCourtChemin = NoeudCommuneService::calculPlusCourtChemin($_POST["nomsCommune"]);
            }catch(ServiceException $e){
                MessageFlash::ajouter('danger', $e->getMessage());
                return ControleurNoeudCommune::rediriger("plusCourtChemin");
            }
            MessageFlash::ajouter('success', 'Temps d\'execution: '.$plusCourtChemin["temps"]." s");
        }

        return ControleurNoeudCommune::afficherVue('vueGenerale.php', $plusCourtChemin);
    }
}