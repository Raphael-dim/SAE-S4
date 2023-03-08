<?php

//use App\PlusCourtChemin\Lib\Psr4AutoloaderClass;
//require_once __DIR__ . '/../src/Lib/Psr4AutoloaderClass.php';
//// instantiate the loader
//$loader = new Psr4AutoloaderClass();
//// register the base directories for the namespace prefix
//$loader->addNamespace('App\PlusCourtChemin', __DIR__ . '/../src');
//// register the autoloader
//$loader->register();


require_once __DIR__ . '/../vendor/autoload.php';
App\PlusCourtChemin\Controleur\RouteurURL::traiterRequete();

