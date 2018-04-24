<?php
  namespace App\Controller\Pages\Profile;

  use App\Entity\User;
  use App\Controller\CustomApi;
  use \DateTime;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class PasswordRecovery extends Controller
  {
    /**
      * @Route("/nouveaumotdepasse/{token}", name="PasswordRecovery")
      */
    public function change_password(Request $request,$token) {
      $api = new CustomApi();

      $res = $api->table_get("password_recovery", array('token' => $token));
      if(sizeof($res) == 0)
        return $this->redirectToRoute('accueil');

      date_default_timezone_set('Europe/Paris');
      $res = $res[0];
      $currentDate = (new DateTime("now"));
      $expDate = date_create($res['expiration_time']);

      if($currentDate < $expDate) {
        $form = $this->get('form.factory')->createNamedBuilder('password_recovery')
        ->add('new_password', PasswordType::class, array('label' => 'Nouveau mot de passe: '))
        ->add('new_password_confirmation', PasswordType::class, array('label' => 'Confirmez nouveau mot de passe: '))
        ->add('subscribe', SubmitType::class, array('label' => 'Je change mon Mot de Passe'));
        $form = $form->getForm();

        if('POST' === $request->getMethod()) {
          $form->handleRequest($request);
          if($request->request->has('password_recovery') && $form->isValid()) {
            $api = new CustomApi();
            $password = $form->getData()['new_password'];
            $password_confirmation = $form->getData()['new_password_confirmation'];

            if(sizeof($password) == 0 || sizeof($password_confirmation) == 0){
              return $this->render('passwordforget.html.twig', array(
                'form' => $form->createView(),
                'error' => "Vous n'avez renseigné aucun mot de passe",
                'state' => "Subscribe2"
              ));
            }

            if( $password != $password_confirmation){
              return $this->render('passwordforget.html.twig', array(
                'form' => $form->createView(),
                'error' => "Votre mot de passe ne correspond pas",
                'state' => "Subscribe2"
              ));
            }

            $password = password_hash($password,PASSWORD_DEFAULT);

            if($currentDate < $expDate) {
              $api->table_update("user",  array('password' =>$password), array('id_user' => $res['id_user']));
              $api->table_delete("password_recovery", array('token' => $token));
              return $this->render('passwordforget.html.twig', array(
                'state' => "Validation2"
              ));
            } else {
              return $this->render('passwordforget.html.twig', array(
                'form' => $form->createView(),
                'error' => "Session expirée ",
                'state' => "Resend"
              ));
            }
          }
        }
        return $this->render('passwordforget.html.twig', array(
          'form' => $form->createView(),
          'error' => Null,
          'state' => "Subscribe2"
        ));

      } else {
        return $this->render('passwordforget.html.twig', array(
          'token' => $res['token'],
          'state' => "Resend"
        ));
      }
    }

    /**
      * @Route("/resend_password_recovery/{token}", name="new_password_recovery")
      */
    public function send_new_mail($token) {
      date_default_timezone_set('Europe/Paris');
      $expirationDate = new DateTime("now");
      $expirationDate->modify("+1 hour");
      $api = new CustomApi();
      $res = $api->table_get("password_recovery", array('token' => $token))[0];
      $new_token = substr(bin2hex(random_bytes(40)), 0, 10);
      $api->table_update("password_recovery", array(
        'token' => $new_token,
        'expiration_time' => date_format($expirationDate, 'Y-m-d H:i:s'),
      ), array('token' => $token));

      $link = "http://localhost:8000/nouveaumotdepasse/" . $new_token;
      $email = $api->table_get("user", array('id_user' => $res['id_user']))[0]['email'];
      $mail_body = array(
        'email' => $email,
        'subject' => "Oubli de mot de passe",
        'html' => "<p>Vous pouvez changer votre mot de passe LiveTree en cliquant sur <u><a href=\"" . $link . "\">ce lien</a></u></p>"
        );
      $api->send_mail($mail_body);

      return $this->render('passwordforget.html.twig', array(
          'state' => "Validation",
          'email' => $email
      ));
    }
  }

 ?>