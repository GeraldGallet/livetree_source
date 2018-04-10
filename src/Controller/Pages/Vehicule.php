<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Vehicule extends Controller
  {
    /**
      * @Route("/vehicules")
      */
    public function loadVehicules() {
      $carName = "ma voiture bleu";
      $ownerName = "JC tokyo drift";
      return $this->render('reservations/vehicules.html.twig', array(
            'carName' => $carName,
            'ownerName' => $ownerName
      ));
    }
  }

  
 ?>
