<?php
  namespace App\Controller\Pages;

  use App\Entity\User;
  use App\Controller\CustomApi;
  use \DateTime;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;

  class PasswordRecovery extends Controller
  {
    /**
      * @Route("/PasswordRecovery/{token}", name="PasswordRecovery")
      */
    public function load_validation($token) {
      $api = new CustomApi();

      $res = $api->table_get("password_recovery", array('token' => $token));
      if(sizeof($res) == 0)
        return $this->redirectToRoute('accueil');

      date_default_timezone_set('Europe/Paris');
      $res = $res[0];
      $currentDate = (new DateTime("now"));
      $expDate = date_create($res['expiration_time']);
      $email = $api->table_get("user", array('id_user' => $res['id_user']))[0]['email'];



      if($currentDate < $expDate) {
        $api->table_update("user",  array('email' => $res['email']));
        $api->table_delete("email_validate", array('token' => $token));
        return $this->render('validation.html.twig', array(
          'success' => true,
          'email' => $res['email']
        ));
      } else {
        return $this->render('validation.html.twig', array(
          'success' => false,
          'email' => $res['email'],
          'token' => $token
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
      $api->table_update("password_recovery", array(
        'token' => substr(bin2hex(random_bytes(40)), 0, 10),
        'expiration_time' => date_format($expirationDate, 'Y-m-d H:i:s'),
      ), array('token' => $token));

      $email = $api->table_get("user", array('id_user' => $res['id_user']))[0]['email'];
      return $this->render('forms/inscription.html.twig', array(
          'email' => $email,
          'visiting' => false,
          'state' => "Validation"
      ));
    }
  }

 ?>
