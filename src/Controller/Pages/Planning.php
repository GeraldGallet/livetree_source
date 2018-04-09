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
      $object = "de la voiture";
      $time1 = array(
        'first' => 0,
        'second' => 2
      );
      $time2 = array(
        'first' => 10,
        'second' => 12
      );
      $time3 = array(
        'first' => 14,
        'second' => 18
      );
      $date = [$time1, $time2, $time3];
      $dates = array(
        '02/04' => $date,
        '03/04' => $date,
        '04/04' => $date,
        '05/04' => $date,
        '06/04' => $date,
        '07/04' => $date,
        '08/04' => $date
      );

      return $this->render('reservations/planning.html.twig', array(
            'object' => $object,
            'dates' => $dates
      ));
    }

    /**
      * @Route("/back", name="go_back")
      */
    public function go_back() {
      // Go back to the current reservation
    }
  }

 ?>
