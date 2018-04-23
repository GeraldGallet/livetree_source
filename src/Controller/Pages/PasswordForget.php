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
      * @Route("/motdepasse")
      */
      public function new(Request $request)
      {
        $api = new CustomApi();

        $form = $this->get('form.factory')->createNamedBuilder('password_forgot')
            ->add('email', EmailType::class, array('label' => 'Email: '))
            ->add('subscribe', SubmitType::class, array('label' => 'J\'envoie un mail'));
        $form = $form->getForm();

        if('POST' === $request->getMethod()) {
          $form->handleRequest($request);

          if($request->request->has('password_forgot') && $form->isValid()) {
            $api = new CustomApi();
            $email = $form->getData()['email'];

            if(sizeof($email) == 0)
              return $this->render('passwordforget.html.twig', array(
                  'form' => $form->createView(),
                  'error' => 'Vous n\'avez renseigné aucun e-mail',
                  'state' => "Subscribe"
              ));

            $res = $api->table_get("user", array('email' => $email));
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
            $new_token = array(
              'token' => $token,
              'id_user' => $res[0]['id_user'],
              'expiration_time' => date_format($expirationDate, 'Y-m-d H:i:s'),
              );

            $link = "http://localhost:8000/nouveaumotdepasse/" . $token;

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
          }

          return $this->render('passwordforget.html.twig', array(
           'form' => $form->createView(),
           'error' => NULL,
           'state' => "Subscribe"
           ));
         }
       }
  ?>
