<?php


use App\PlusCourtChemin\Lib\ConnexionUtilisateur;
use App\PlusCourtChemin\Lib\Conteneur;

$generateurUrl = Conteneur::recupererService("generateurUrl");
$assistantUrl = Conteneur::recupererService("assistantUrl");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= $pagetitle ?></title>
    <link rel="stylesheet" href="<?= $assistantUrl->getAbsoluteUrl("assets/css/global.css") ?>">
    <link rel="stylesheet" href="<?= $assistantUrl->getAbsoluteUrl("assets/css/keyFrames.css") ?>">
    <link rel="stylesheet" href="<?= $assistantUrl->getAbsoluteUrl("assets/css/nav.css") ?>">
</head>

<body>
<header>
    <?php
    require __DIR__ . "/nav.php";
    ?>
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
    <div id="content">
    <?php
    /**
     * @var string $cheminVueBody
     */
    require __DIR__ . "/{$cheminVueBody}";
    ?>
    </div>
</main>
<footer>
    <p>
        Copyleft Romain Lebreton
    </p>
</footer>
</body>

</html>