<link href="<?= $assistantUrl->getAbsoluteUrl("assets/css/confirm.css") ?>" rel="stylesheet">
<div class="blur">
</div>
<div class="over">
    <form method="post" action="<?= $url ?>">
        <p style="padding: 20px; max-width: 80%; margin: auto"><label
                    style="color: white"><?php echo $message ?></label></p>
        <?php if (isset($mdp)) {
            echo '<label style="color: white; margin: 30px" for="mdp_id">Confirmation du mot de passe : </label>
                  <input type="password" name="mdp" id="mdp_id">';
        } ?>

        <Button id="bt1" class="nav" type="submit" name="cancel" value="Annuler">Annuler</Button>
        <Button id="bt2" class="nav" type="submit" name="confirm" value="Confirmer">Confirmer</Button>

    </form>
</div>
