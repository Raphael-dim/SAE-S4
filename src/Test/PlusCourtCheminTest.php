<?php

use App\PlusCourtChemin\Modele\Repository\NoeudRoutierRepository;
use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;
use PHPUnit\Framework\TestCase;

class PlusCourtCheminTest extends TestCase {

    private $noeudRoutierRepository;

    //On réinitialise l'ensemble avant chaque test
    protected function setUp(): void
    {
        $this->noeudRoutierRepository = new NoeudRoutierRepository();
    }

    public function testPathBetween2($nomCommune1, $nomCommune2) {
        $result = NoeudCommuneService::calculPlusCourtChemin([$nomCommune1,$nomCommune2]);

        $this->assertNotNull($result, "Le chemin est null");
        $this->assertNotEmpty($result, "Le chemin est vide");
        $this->assertIsArray($result, "Le chemin n'est pas un array");
    }

    public function testPathBetween2Random() {
        $randomNom1 = (new NoeudCommuneRepository())->recupererPar(["id_rte500" => random_int(1,910833)])[0]->getNomCommune();
        $randomNom2 = (new NoeudCommuneRepository())->recupererPar(["id_rte500" => random_int(1,910833)])[0]->getNomCommune();

        $this->testPathBetween2($randomNom1, $randomNom2);
    }

    public function testPathBetweenFranceCorse() {
        $gidFrance = random_int(1,910833);
        $gidCorse = random_int(1,910833);

        $this->testPathBetween2($gidFrance, $gidCorse);
    }

    /** test1000RandomPath
     * Test plusieurs chemins aléatoire
     *
     * !ATTENTION! 10 itérations en configuration Postgre  ~30 secondes de test */
    public function test100RandomPath() {
        for($i = 0;$i<100;$i++){
            $this->testChemin2VillesRandom();
        }
    }

    /*public function testAjout() {
        $this->assertFalse($this->ensembleTeste->contient(7));
        $this->ensembleTeste->ajouter(7);
        $this->assertTrue($this->ensembleTeste->contient(7));
        $this->assertEquals(1, $this->ensembleTeste->getTaille());
        //On n'ajoute pas deux fois dans un ensemble, donc la taille doit rester à 1
        $this->ensembleTeste->ajouter(7);
        $this->assertEquals(1, $this->ensembleTeste->getTaille());
    }

    public function testPop() {
        $this->ensembleTeste->ajouter(1);
        $this->ensembleTeste->ajouter(2);
        $this->ensembleTeste->ajouter(3);
        $this->assertEquals(3, $this->ensembleTeste->pop());
        $this->assertEquals(2, $this->ensembleTeste->pop());
        $this->assertEquals(1, $this->ensembleTeste->pop());
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("L'ensemble est vide!");
        $this->ensembleTeste->pop();
    }*/
}
