<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Planning extends Controller
  {
    /**
      * @Route("/planning")
      */
    public function load_planning() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      return $this->render('reservations/planning.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

 ?>
