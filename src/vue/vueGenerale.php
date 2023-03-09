<?php


use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Lib\Conteneur;

$generateurUrl = Conteneur::recupererService("generateurUrl");
$assistantUrl = Conteneur::recupererService("assistantUrl");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= $pagetitle ?></title>
    <link rel="stylesheet" href="<?= $assistantUrl->getAbsoluteUrl("assets/css/navstyle.css") ?>">
    <link rel="stylesheet" href="<?= $assistantUrl->getAbsoluteUrl("assets/css/global.css") ?>">
    <link rel="stylesheet" href="<?= $assistantUrl->getAbsoluteUrl("assets/css/keyFrames.css") ?>">
</head>

<body>
<header>
    <nav>
        <ul>
            <li>
                <a href="<?= $generateurUrl->generate("utilisateurs") ?>">Utilisateurs</a>
            </li>
            <li>
                <a href="<?= $generateurUrl->generate("communes") ?>">Communes</a>
            </li>
            <?php

            if (!ConnexionUtilisateur::estConnecte()) {
                echo '
                    <li>
                        <a href="./connexion">
                            <img alt="login" src="' . $assistantUrl->getAbsoluteUrl("assets/img/enter.png") . '" width="18">
                        </a>
                    </li>';
            } else {
                $loginHTML = htmlspecialchars(ConnexionUtilisateur::getLoginUtilisateurConnecte());
                $loginURL = rawurlencode(ConnexionUtilisateur::getLoginUtilisateurConnecte());
                echo <<<HTML
                    <li>
                        <a href="detailUtilisateur/$loginURL">
                            <img alt="user" src="../ressources/img/user.png" width="18">
                            $loginHTML
                        </a>
                    </li>
                    <li>
                        <a href="./deconnecter">
                            <img alt="logout" src="../ressources/img/logout.png" width="18">
                        </a>
                    </li>
                    HTML;
            }
            ?>
        </ul>
    </nav>
    <div class="pileFlash">
        <?php
        foreach (["success", "info", "warning", "danger"] as $type) {
            foreach ($messagesFlash[$type] as $messageFlash) {
                echo <<<HTML
                    <div class="alert alert-$type">
                        $messageFlash
                    </div>
                    HTML;
            }
        }
        ?>
    </div>
</header>
<main>
    <?php
    /**
     * @var string $cheminVueBody
     */
    require __DIR__ . "/{$cheminVueBody}";
    ?>
</main>
<footer>
    <p>
        Copyleft Romain Lebreton
    </p>
</footer>
</body>
</html>