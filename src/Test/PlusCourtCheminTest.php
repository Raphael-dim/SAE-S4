<?php

use App\PlusCourtChemin\Modele\Repository\NoeudRoutierRepository;
use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;
use App\PlusCourtChemin\Service\Exception\ServiceException;
use App\PlusCourtChemin\Service\NoeudCommuneService;
use PHPUnit\Framework\TestCase;

class PlusCourtCheminTest extends TestCase {

    private $noeudRoutierRepository;

    //On réinitialise l'ensemble avant chaque test
    protected function setUp(): void
    {
        $this->noeudRoutierRepository = new NoeudRoutierRepository();
    }

    private function testPathBetween2($nomCommune1, $nomCommune2) {
        try {
            $result = NoeudCommuneService::calculPlusCourtChemin([$nomCommune1,$nomCommune2]);
        } catch (ServiceException $e) {
            $this->assertTrue(true, $e->getMessage());
            return;
        }

        $this->assertNotNull($result, "Le chemin est null");
        $this->assertNotEmpty($result, "Le chemin est vide");
        $this->assertIsArray($result, "Le chemin n'est pas un array");
    }

    public function testPathBetween2Random() {
        $randomNom1 = (new NoeudCommuneRepository())->recupererPar(["gid" => random_int(1,34836)])[0]->getNomCommune();
        $randomNom2 = (new NoeudCommuneRepository())->recupererPar(["gid" => random_int(1,34836)])[0]->getNomCommune();

        $this->testPathBetween2($randomNom1, $randomNom2);
    }

    public function testPathBetweenFranceCorse() {
        $nomFrance = "Paris";
        $nomCorse = "Bastia";

        $this->testPathBetween2($nomFrance, $nomCorse);
    }

    public function testPathBetweenUnexisting() {
        $nomFrance = "Inexistant";
        $nomCorse = "VilleQuiExistePas";

        $this->testPathBetween2($nomFrance, $nomCorse);
    }

    /** test1000RandomPath
     * Test plusieurs chemins aléatoire
     *
     * !ATTENTION! 20 itérations en configuration Postgres ~1 minute de test */
    public function test20RandomPath() {
        for($i = 0;$i<20;$i++){
            $this->testPathBetween2Random();
        }
    }
}
