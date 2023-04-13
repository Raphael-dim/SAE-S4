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

    private function testCheminEntreX($nomsCommune) {
        try {
            $result = NoeudCommuneService::calculPlusCourtChemin($nomsCommune);
        } catch (ServiceException $e) {
            echo "ERREUR GERE, " . $e->getMessage();
            $this->assertTrue(true, $e->getMessage());
            return;
        }

        $this->assertNotNull($result, "Le chemin est null");
        $this->assertNotEmpty($result, "Le chemin est vide");
        $this->assertIsArray($result, "Le chemin n'est pas un array");
    }

    public function testCheminEntre2Aleatoire() {
        $randomNom1 = (new NoeudCommuneRepository())->recupererPar(["gid" => random_int(1,34836)])[0]->getNomCommune();
        $randomNom2 = (new NoeudCommuneRepository())->recupererPar(["gid" => random_int(1,34836)])[0]->getNomCommune();

        $this->testCheminEntreX([$randomNom1, $randomNom2]);
    }

    public function testCheminEntre4Aleatoire() {
        $randomNom1 = (new NoeudCommuneRepository())->recupererPar(["gid" => random_int(1,34836)])[0]->getNomCommune();
        $randomNom2 = (new NoeudCommuneRepository())->recupererPar(["gid" => random_int(1,34836)])[0]->getNomCommune();
        $randomNom3 = (new NoeudCommuneRepository())->recupererPar(["gid" => random_int(1,34836)])[0]->getNomCommune();
        $randomNom4 = (new NoeudCommuneRepository())->recupererPar(["gid" => random_int(1,34836)])[0]->getNomCommune();

        $this->testCheminEntreX([$randomNom1, $randomNom2, $randomNom3, $randomNom4]);
    }

    public function testCheminEntre2FranceCorse() {
        $nomFrance = "Paris";
        $nomCorse = "Bastia";

        $this->testCheminEntreX([$nomFrance, $nomCorse]);
    }

    public function testCheminEntre4FranceCorse() {
        $nomFrance1 = "Paris";
        $nomCorse = "Bastia";
        $nomFrance2 = "Montpellier";
        $nomFrance3 = "Sevran";

        $this->testCheminEntreX([$nomFrance1, $nomFrance2, $nomCorse, $nomFrance3]);
    }

    public function testCheminEntreInexistants() {
        $nomInexistant1 = "Inexistant";
        $nomInexistant2 = "VilleQuiExistePas";

        $this->testCheminEntreX([$nomInexistant1, $nomInexistant2]);
    }

    /** test10CheminAleatoire
     * Test plusieurs chemins aléatoire
     *
     * !ATTENTION! 10 itérations en configuration Postgres ~30 secondes de test */
    public function test10CheminEntre2Aleatoire() {
        for($i = 0;$i<10;$i++){
            $this->testCheminEntre2Aleatoire();
        }
    }

    /** test10CheminAleatoire
     * Test plusieurs chemins aléatoire
     *
     * !ATTENTION! 10 itérations en configuration Postgres ~1 minute 30 de test */
    public function test10CheminEntre4Aleatoire() {
        for($i = 0;$i<10;$i++){
            $this->testCheminEntre4Aleatoire();
        }
    }
}
