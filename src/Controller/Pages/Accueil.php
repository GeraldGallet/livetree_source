<?php
  namespace App\Controller\Pages;

  use App\Entity\Connection;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class Accueil extends Controller
  {
    /**
      * @Route("/", name="accueil")
      */
    public function load_accueil() {
      if ( ! session_id() ) @ session_start();
      if(!isset($_SESSION['email'])) {
        $user = new Connection();
        $connection_form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('connect', SubmitType::class, array('label' => 'Se connecter'))
            ->getForm();
      } else {
        $connection_form = NULL;
      }

      if ($form->isSubmitted() && $form->isValid()) {
          $user = $form->getData();


          return $this->redirectToRoute('accueil');
      }

      return $this->render('accueil.html.twig', array(
            'connection_form' => $connection_form->createView(),
      ));
    }
  }

 ?>
