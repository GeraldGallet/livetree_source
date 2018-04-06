<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Validation extends Controller
  {
    /**
      * @Route("/validation", name="validation")
      */
    public function load_validation() {
      $email = "gerald.gallet@isen.yncrea.fr";

      return $this->render('validation.html.twig', array(
            'email' => $email
      ));
    }
  }

 ?>
