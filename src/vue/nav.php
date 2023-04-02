<?php

use App\PlusCourtChemin\Lib\ConnexionUtilisateur;

?>
<nav>
    <ul id="Menu">
        <li class="grosmenu">
            <a href="<?= $generateurUrl->generate("utilisateurs") ?>">Utilisateurs</a>
        </li>
        <li class="grosmenu">
            <a href="<?= $generateurUrl->generate("plusCourtChemin") ?>">Carte</a>
        </li>
        <li class="grosmenu">
            <a href="<?= $generateurUrl->generate("communes") ?>">Liste des communes</a>
        </li>
                <?php

        if (!ConnexionUtilisateur::estConnecte()) {
            echo '
                    <li class="grosmenu user">
                        <a href="' . $generateurUrl->generate("connexion") . '">
                            <img alt="login" src="' . $assistantUrl->getAbsoluteUrl("assets/img/enter.png") . '" width="18">
                        </a>
                    </li>';
        } else {
            $loginHTML = htmlspecialchars(ConnexionUtilisateur::getLoginUtilisateurConnecte());
            $loginURL = rawurlencode(ConnexionUtilisateur::getLoginUtilisateurConnecte());
            echo '
                    <li class="grosmenu user">
                        <a  href="' . $generateurUrl->generate("detailUtilisateur", ["idUtilisateur" => $loginURL]) . '">
                            <img alt="user" src="' . $assistantUrl->getAbsoluteUrl("assets/img/user.png") . '" width="18">
                            ' . $loginHTML . '
                        </a>
                    </li>
                    <li class="grosmenu">
                        <a href="' . $generateurUrl->generate("deconnecter") . '">
                            <img alt="logout" src="' . $assistantUrl->getAbsoluteUrl("assets/img/logout.png") . '" width="18">
                        </a>
                    </li>
                    ';
        }
        ?>
    </ul>
</nav>