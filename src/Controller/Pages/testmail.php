<?php

  namespace App\Controller\Pages;
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class testmail extends Controller {

    /**
      * @Route("/test", name="test_mail")
      */
    function load_test() {

      $mail = 'mailsender59270@gmail.com'; // Déclaration de l'adresse de destination.

      if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui présentent des bogues.
      {
        $passage_ligne = "\r\n";
      } else
      {
        $passage_ligne = "\n";
      }

      //=====Déclaration des messages au format texte et au format HTML.

      $mail = new PHPmailer();
      // Paramètres SMTP
       $mail->IsSMTP();
       $mail->SMTPDebug = 4; // activation des fonctions SMTP
       $mail->SMTPAuth = true; // on l’informe que ce SMTP nécessite une autentification
       $mail->SMTPSecure = 'tls'; // protocole utilisé pour sécuriser les mails 'ssl' ou 'tls'
       $mail->Host = "smtp.gmail.fr"; // définition de l’adresse du serveur SMTP : 25 en local, 465 pour ssl et 587 pour tls
       $mail->Port = 587;
       $mail->SMTPAuth = true; // définition du port du serveur SMTP
       $mail->Username = "sendmailer59270@gmail.com"; // le nom d’utilisateur SMTP
       $mail->Password = "b6ef6e93"; // son mot de passe SMTP

      // Paramètres du mail
       $mail->AddAddress('sendmailer59270@gmail.com','robin'); // ajout du destinataire
       $mail->setFrom("sendmailer59270@gmail.com","Expediteur"); // adresse mail de l’expéditeur
       $mail->AddReplyTo("sendmailer59270@gmail.com","Expediteur"); // adresse mail et nom du contact de retour
       $mail->IsHTML(true); // envoi du mail au format HTML
       $mail->Subject = "Sujet"; // sujet du mail
       $mail->msgHTML(file_get_contents('C:\wamp64\www\livetree_source\src\Controller\Pages\mail.html')); // le corps de texte du mail en HTML
       $mail->AltBody = "bonjour";
       if(!$mail->Send()) { // envoi du mail
           echo "Mailer Error: " . $mail->ErrorInfo; // affichage des erreurs, s’il y en a
       }
       
       else {
          echo  "Le message a bien été envoyé !";
        }
            return $this->render('test_mail.html.twig');
    }
  }

  ?>
