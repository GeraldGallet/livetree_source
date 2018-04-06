<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Bornes extends Controller
  {
    /**
      * @Route("/bornes")
      */
    public function load_bornes() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      return $this->render('reservations/bornes.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

 ?>
