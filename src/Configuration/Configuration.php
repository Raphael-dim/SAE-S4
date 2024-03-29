<?php

namespace App\PlusCourtChemin\Configuration;

use Exception;

class  Configuration
{

    // la variable debug est un boolean
    static private bool $debug = true;

    public ConfigurationBDDInterface $configurationBDD;

    public function __construct(ConfigurationBDDInterface $configurationBDD)
    {
        $this->configurationBDD= $configurationBDD;
    }

    public function getConfigurationBDD(): ConfigurationBDDInterface
    {
        return $this->configurationBDD;
    }

    static public function getDebug(): bool
    {
        return Configuration::$debug;
    }

    public static function getDureeExpirationSession() : string
    {
        // Durée d'expiration des sessions en secondes
        return 3600;
    }

    public static function getAbsoluteURL() : string
    {
        return("../web/controleurFrontal.php");
        throw new Exception("URL absolue non renseignée !");
    }

}