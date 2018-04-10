<?php
  namespace App\Controller\Pages;

  use App\Entity\Connection;
  use App\Controller\CustomApi;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class Accueil extends Controller
  {
    /**
      * @Route("/", name="accueil")
      */
    public function load_accueil(Request $request) {
      session_start();

      if(!isset($_SESSION['id_user'])) {
        $connected = false;
        $last_name = NULL;
        $first_name = NULL;
        $user = new Connection();

        $connection_form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('connect', SubmitType::class, array('label' => 'Se connecter'))
            ->getForm();
        $connection_form->handleRequest($request);
        $view = $connection_form->createView();

        if ($connection_form->isSubmitted() && $connection_form->isValid())
        {
          $user = $connection_form->getData();
          $api = new CustomApi();
          $db_user = $api->user_get($user->getEmail());

          if($user->getPassword() == $db_user['password'])
          {
            $_SESSION['email'] = $db_user['email'];
            $_SESSION['id_user'] = $db_user['id_user'];
            $_SESSION['first_name'] = $db_user['first_name'];
            $_SESSION['last_name'] = $db_user['last_name'];
            $_SESSION['id_status'] = $db_user['id_status'];
            $_SESSION['phone_number'] = $db_user['phone_number'];
          } else
          {
            return $this->render('accueil.html.twig', array(
              'connected' => $connected,
              'connection_form' => $view,
              'error' => 'La combinaison email/mot de passe n\'existe pas !',
              'last_name' => $last_name,
              'first_name' => $first_name
            ));
          }

          return $this->redirectToRoute('accueil');
        }
      } else {
        $connected = true;
        $view = NULL;
        $last_name = $_SESSION['last_name'];
        $first_name = $_SESSION['first_name'];
      }

      return $this->render('accueil.html.twig', array(
            'connected' => $connected,
            'connection_form' => $view,
            'error' => NULL,
            'last_name' => $last_name,
            'first_name' => $first_name
      ));
    }

    /**
      * @Route("/deconnexion", name="deconnect")
      */
    public function deconnect() {
      session_start();
      session_destroy();
      return $this->redirectToRoute('accueil');
    }
  }

 ?>
