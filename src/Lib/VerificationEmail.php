<?php

namespace App\PlusCourtChemin\Lib;

use App\PlusCourtChemin\Configuration\Configuration;
use App\PlusCourtChemin\Modele\DataObject\Utilisateur;
use App\PlusCourtChemin\Modele\Repository\UtilisateurRepository;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class VerificationEmail
{
    public static function envoiEmailValidation(Utilisateur $utilisateur): void
    {
        $loginURL = rawurlencode($utilisateur->getLogin());
        $nonceURL = rawurlencode($utilisateur->getNonce());
        $generateurUrl = Conteneur::recupererService("generateurUrl");
        $url = $generateurUrl->generate('validerEmail', ["idUtilisateur" => $loginURL, "nonce" => $utilisateur->getNonce()]);
        //$url = "http://localhost/SAE%20Semestre%204/SAE-S4/web/validerEmail/" . $loginURL . "/" . $nonceURL;
        $corpsEmail = '
        <!DOCTYPE html>
        <head>
            <meta charset="utf-8">
            <title>Vérification de compte</title>
        </head>
        <body style="background-color: #012e49; margin-left: 15px; padding: 15px">
            <h1 style="color: white">Bienvenue sur notre plateforme de cartographie !</h1>
            <p style="color: white">Merci de vous être inscrit sur notre plateforme. Pour finaliser votre inscription, veuillez cliquer sur le bouton
                ci-dessous pour vérifier votre compte.</p>
            <a style="color: #3c763d" href="' . $url . '">Vérifier mon compte</a>
            <p style="color: white">Si vous n\'avez pas demandé à vous inscrire sur notre plateforme, veuillez ignorer cet e-mail.</p>
            <p style="color: white">Cordialement,</p>
            <p style="color: white">L\'équipe de vote en ligne</p>
        </body>
        </html>';

        $transport = Transport::fromDsn('smtp://vote.IUTms@gmail.com:kilbhfnytfuxgsuu@smtp.gmail.com:587?verify_peer=0');

        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('iutms.sae.map@gmail.com')
            ->to($utilisateur->getEmailAValider())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Lien de vérification')
            //->text($corpsEmail)
            ->html($corpsEmail);

        //try {
        $mailer->send($email);
        //} catch (TransportExceptionInterface $e) {
        //}

        MessageFlash::ajouter('info', 'Un mail vient d\'être envoyé.');
    }

    public static function traiterEmailValidation($login, $nonce): bool
    {
        $utilisateurRepository = new UtilisateurRepository();
        /** @var Utilisateur $utilisateur */
        $utilisateur = $utilisateurRepository->recupererParClePrimaire($login);

        if ($utilisateur === null)
            return false;

        if ($utilisateur->getNonce() !== $nonce) {
            return false;
        }

        $utilisateur->setEmail($utilisateur->getEmailAValider());
        $utilisateur->setEmailAValider("");
        $utilisateur->setNonce("");

        $utilisateurRepository->mettreAJour($utilisateur);
        return true;
    }

    public static function aValideEmail(Utilisateur $utilisateur): bool
    {
        return $utilisateur->getEmail() !== "";
    }
}