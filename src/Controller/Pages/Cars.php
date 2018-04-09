<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Cars extends Controller
  {
    /**
      * @Route("/voitures")
      */
    public function load_cars() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      return $this->render('reservations/cars.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

 ?>
