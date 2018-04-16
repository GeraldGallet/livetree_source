<?php
  namespace App\Controller\Pages;

  use App\Controller\CustomApi;

  use \DateTime;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Validation extends Controller
  {
    /**
      * @Route("/validation/{token}", name="validation")
      */
    public function load_validation($token) {
      $api = new CustomApi();

      $res = $api->table_get("email_validate", array('token' => $token));
      if(sizeof($res) == 0)
        return $this->redirectToRoute('accueil');

      date_default_timezone_set('Europe/Paris');
      $res = $res[0];
      $currentDate = (new DateTime("now"));
      $expDate = date_create($res['expiration_time']);
      $email = $api->table_get("user", array('id_user' => $res['id_user']))[0]['email'];

      if($currentDate < $expDate) {
        $api->table_update("user", array('activated' => true), array('id_user' => $res['id_user']));
        $api->table_delete("email_validate", array('token' => $token));
        return $this->render('validation.html.twig', array(
          'success' => true,
          'email' => $res['email']
        ));
      } else {
        return $this->render('validation.html.twig', array(
          'success' => false,
          'email' => $res['email'],
          'id_user' => $res['id_user'],
          'token' => $token
        ));
      }


    }

    /**
      * @Route("/resend/{token}", name="new_mail")
      */
    public function send_new_mail($token) {
      date_default_timezone_set('Europe/Paris');
      $expirationDate = new DateTime("now");
      $expirationDate->modify("+1 hour");
      $api = new CustomApi();
      $res = $api->table_get("email_validate", array('token' => $token))[0];
      $api->table_update("email_validate", array(
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
