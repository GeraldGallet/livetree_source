<?php
  namespace App\Controller\Pages\Profile;

  use App\Controller\CustomApi;

  use \DateTime;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;
//Classes controlant la validation d'un compte apres inscription
  class AccountValidation extends Controller
  {
    /**
      * @Route("/validation/{token}", name="validation")
      */
    public function load_validation($token) {
      $api = new CustomApi();//L'interface pour l'API

      $res = $api->table_get("email_validate", array('token' => $token)); //On vérie que le token existe dans notre BDD
      if(sizeof($res) == 0)//Sinon on le redirige vers la page accueil
        return $this->redirectToRoute('accueil');

      date_default_timezone_set('Europe/Paris');
      $res = $res[0];
      $currentDate = (new DateTime("now"));
      $expDate = date_create($res['expiration_time']);
      $email = $api->table_get("user", array('id_user' => $res['id_user']))[0]['email'];//On recupère l'email de l'usager arrivant sur la page frâce au token qui comporte l'id user

      if($currentDate < $expDate) {//On vérifie que la date contenue dans le token ne soit pas expirée
        $api->table_update("user", array('activated' => true), array('id_user' => $res['id_user']));//On active le compte
        $api->table_delete("email_validate", array('token' => $token));//On supprime le token
        return $this->render('validation.html.twig', array(//On informe que le compte est valider à l'utilisateur
          'success' => true,
          'email' => $res['email']
        ));
      } else {//Si le token est expiré on permet à l'utilisateur d'en obtenir un nouveaux
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
      // Fonction qui renvoie un nouveau token via e-mail
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
