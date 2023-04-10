<?php

namespace App\PlusCourtChemin\Test;

use App\PlusCourtChemin\Modele\Repository\UtilisateurRepository;
use Ensemble;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{
    private $UtilisateurRepository;

    //On réinitialise l'ensemble avant chaque test
    protected function setUp(): void
    {
        $this->UtilisateurRepository = new UtilisateurRepository();
    }

    public function creerHTMLCharUtilisateur(){

    }
}
