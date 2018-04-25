<?php

  namespace App\Controller\Pages\Profile;
  use App\Entity\User;
  use App\Controller\CustomApi;
  use \DateTime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\FormEvent;

  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
// Classes controlant le mot de passe oublié
  class PasswordForget extends Controller {

    /**
      * @Route("/motdepasse")
      */
      public function new(Request $request)
      {
        $api = new CustomApi();//L'interface pour l'API

        $form = $this->get('form.factory')->createNamedBuilder('password_forgot')//Création d'un formulaire pour renseigner l'adresse mail du compte oublié
            ->add('email', EmailType::class, array('label' => 'Email: '))
            ->add('subscribe', SubmitType::class, array(
              'label' => 'J\'envoie un mail',
              'attr' => [
                'class' => "btn btn-outline-secondary",
                ]));
        $form = $form->getForm();

        if('POST' === $request->getMethod()) { //On vérifie que le formulaire  été envoyé
          $form->handleRequest($request);

          if($request->request->has('password_forgot') && $form->isValid()) { // On vérifie si le formulaire est valide
            $api = new CustomApi();// L'interface pour l'API
            $email = $form->getData()['email'];// On récupère l'adresse renseignée

            if(sizeof($email) == 0) //On regarde si l'adresse est pas vide sinon on lui affiche un message d'erreur
              return $this->render('passwordforget.html.twig', array(
                  'form' => $form->createView(),
                  'error' => 'Vous n\'avez renseigné aucun e-mail',
                  'state' => "Subscribe"
              ));

            $res = $api->table_get("user", array('email' => $email));// On regarde si l'adresse existe dans la BDD sinon message d'erreur
            if(sizeof($res) == 0)
              return $this->render('passwordforget.html.twig', array(
                  'form' => $form->createView(),
                  'error' => "Cet e-mail n'est pas enregistrée sur ce site",
                  'state' => "Subscribe"
              ));

            date_default_timezone_set('Europe/Paris');
            $expirationDate = new DateTime("now");
            $expirationDate->modify("+1 hour");
            $token =  substr(bin2hex(random_bytes(40)), 0, 10);
            $new_token = array( //On crée un Token de 10 charactères aléatoires ayant une date d'expiration et l'id du user faisant la demande
              'token' => $token,
              'id_user' => $res[0]['id_user'],
              'expiration_time' => date_format($expirationDate, 'Y-m-d H:i:s'),
              );

            $link = "http://localhost:8000/nouveaumotdepasse/" . $token; //On crée le lien avec le token pour rejoindre la page pour regénéré un mot de de passe

            $mail_body = array( // On génére le mail avec l'adresse mail renseignée, ce mail comporte le lien pour le nouveau mot de passe
              'email' => $email,
              'subject' => "Oubli de mot de passe",
              'html' => "<p>Vous pouvez changer votre mot de passe LiveTree en cliquant sur <u><a href=\"" . $link . "\">ce lien</a></u></p>"
              );

            $api->table_add("password_recovery", $new_token);
            $api->send_mail($mail_body);// On envoie le mail grâce a NodeMailer
            return $this->render('passwordforget.html.twig', array(// On renvoie Un nouveau statut sur la page pour permettre à l'utilisateur d'accèder au formulaire du nouveau mot de passe
                'email' => $email,
                'state' => "Validation"
            ));

            }
          }

          return $this->render('passwordforget.html.twig', array( //On affiche le formulaire por renseigner l'adresse mail
           'form' => $form->createView(),
           'error' => NULL,
           'state' => "Subscribe"
           ));
         }
       }
  ?>
