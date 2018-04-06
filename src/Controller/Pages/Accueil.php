<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Accueil extends Controller
  {
    /**
      * @Route("/")
      */
    public function load_accueil() {
      $first_name = "Gérald";
      $last_name = "Poiret";
      return $this->render('accueil.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

 ?>
