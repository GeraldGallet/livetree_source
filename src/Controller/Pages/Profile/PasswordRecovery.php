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
// Classes controlant le nouveau mot de passe
  class PasswordRecovery extends Controller
  {
    /**
      * @Route("/nouveaumotdepasse/{token}", name="PasswordRecovery")
      */
    public function change_password(Request $request,$token) {
      $api = new CustomApi();//L'interface pour l'api

      $res = $api->table_get("password_recovery", array('token' => $token));//On regarde si le token du lien existe dans notre BDD
      if(sizeof($res) == 0)//Si non on renvoie le visiteur sur l'accueil
        return $this->redirectToRoute('accueil');

      date_default_timezone_set('Europe/Paris');
      $res = $res[0];
      $currentDate = (new DateTime("now"));
      $expDate = date_create($res['expiration_time']);//On récupere la date d'expiration liée au token

      if($currentDate < $expDate) { // On vérifie que la date d'expiration et toujours valide
        $form = $this->get('form.factory')->createNamedBuilder('password_recovery')// Si oui on crée le formulaire pour que l'utilisateur puisse renseigné son nouveau mot de passe
        ->add('new_password', PasswordType::class, array('label' => 'Nouveau mot de passe: '))
        ->add('new_password_confirmation', PasswordType::class, array('label' => 'Confirmez nouveau mot de passe: '))
        ->add('subscribe', SubmitType::class, array('label' => 'Je change mon Mot de Passe'));
        $form = $form->getForm();
        //On récupere le formulaire on vérifie qu'il est valide
        if('POST' === $request->getMethod()) {
          $form->handleRequest($request);
          if($request->request->has('password_recovery') && $form->isValid()) {
            $api = new CustomApi();
            //On récupère les données renseignées
            $password = $form->getData()['new_password'];
            $password_confirmation = $form->getData()['new_password_confirmation'];

            if(sizeof($password) == 0 || sizeof($password_confirmation) == 0){ // On vérifie que les deux champs ont été renseigné
              return $this->render('passwordforget.html.twig', array(// Si non on lui affiche un message d'erreur
                'form' => $form->createView(),
                'error' => "Vous n'avez renseigné aucun mot de passe",
                'state' => "Subscribe2"
              ));
            }

            if( $password != $password_confirmation){//On vérifie que le mot de passe et sa confirmation correspondent
              return $this->render('passwordforget.html.twig', array(// Sinon on uli affiche une erreur
                'form' => $form->createView(),
                'error' => "Votre mot de passe ne correspond pas",
                'state' => "Subscribe2"
              ));
            }

            $password = password_hash($password,PASSWORD_DEFAULT);//On hash+ salt le mot de passe afin de le stocké dans la BDD


            $api->table_update("user",  array('password' =>$password), array('id_user' => $res['id_user']));//On va stocker le nouveau mot de passe
            $api->table_delete("password_recovery", array('token' => $token));// on détruit le token
            return $this->render('passwordforget.html.twig', array(// On annonce à l'utilisateur que la démarche c'est bien passé
              'state' => "Validation2"
            ));

          }
        }
        return $this->render('passwordforget.html.twig', array(//On affiche le formulaire pour que l'utilisateur puisse renseigner son nouveau mot de passe
          'form' => $form->createView(),
          'error' => Null,
          'state' => "Subscribe2"
        ));

      } else {// Si le token est expiré on annonce que la session a expiré et on lui permet d'en obtenir un nouveau
        return $this->render('passwordforget.html.twig', array(
          'token' => $res['token'],
          'state' => "Resend"
        ));
      }
    }

    /**
      * @Route("/resend_password_recovery/{token}", name="new_password_recovery")
      */

    //Fonction qui a pour but de renvoyer un token à l'utilisateur si le sien a expiré
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
