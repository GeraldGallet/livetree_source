<?php

  namespace App\Controller\Pages;
  use App\Entity\User;
  use App\Controller\CustomApi;
  use \DateTime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\FormEvent;

  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class PasswordForget extends Controller {

    /**
      * @Route("/passwordForget")
      */
      public function new(Request $request)
      {



        $user = new User();
        $api = new CustomApi();

        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class, array('label' => 'Email: '))
            ->add('subscribe', SubmitType::class, array('label' => 'J\'envoie un mail'));
        $form = $form->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() )  {
            $user = $form->getData();
            $api = new CustomApi();
            $email = $user->getEmail();
            $res = $api->table_get("user", array('email' => $email));
            if(sizeof($res) == 0)
              return $this->render('passwordforget.html.twig', array(
                  'form' => $form->createView(),
                  'error' => "Votre e-mail n'est pas enregistrée sur ce site",
                  'state' => "Subscribe"
              ));
            date_default_timezone_set('Europe/Paris');
            $expirationDate = new DateTime("now");
            $token =  substr(bin2hex(random_bytes(40)), 0, 10);
            $new_token = array(
              'token' => $token,
              'email' => $email,
              'expiration_time' => date_format($expirationDate, 'Y-m-d H:i:s'),
              );
            $link = "http://localhost:8000/PasswordRecovery/" . $token;
            $mail_body = array(
              'email' => $email,
              'subject' => "Oubli de mot de passe",
              'html' => "<p>Vous pouvez changer votre mot de passe LiveTree en cliquant sur <u><a href=\"" . $link . "\">ce lien</a></u></p>"
              );
            $api->table_add("password_recovery", $new_token);
            $api->send_mail($mail_body);
            return $this->render('passwordforget.html.twig', array(
                'email' => $email,
                'state' => "Validation"
            ));

          }
          else
           {
             return $this->render('passwordforget.html.twig', array(
              'form' => $form->createView(),
              'error' => NULL,
              'state' => "Subscribe"
                ));
          }
      }








  }
  ?>
