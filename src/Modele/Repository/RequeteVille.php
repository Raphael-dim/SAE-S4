<?php
use App\PlusCourtChemin\Modele\Repository\NoeudCommuneRepository;

// A COMPLETER

// récupération du contenu du champ, passé en get
$ville = $_GET['ville'];

// lancement de la requête SQL avec selectByName et
// récupération du résultat de la requête SQL
$tab = (new NoeudCommuneRepository)->recuperer(0, 5, $ville);

// délai fictif
// sleep(1);

// affichage en format JSON du résultat précédent
echo json_encode($tab);
