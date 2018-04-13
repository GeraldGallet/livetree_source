<?php
  namespace App\Controller\Pages;

  use App\Controller\CustomApi;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class History extends Controller
  {
    /**
      * @Route("/profil/historique")
      */
    public function load_history() {
      session_start();

      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');


      $resa_borne = [];
      $resa_car = [];
      $api = new CustomApi();

      foreach($api->reservation_borne_get_by_user($_SESSION['id_user']) as $resa) {
        array_push($resa_borne, array(
          'date_resa' => substr($resa['date_resa'], 0, 10),
          'start_time' => substr($resa['start_time'], 0, 5),
          'end_time' => substr($resa['end_time'], 0, 5),
          'charge' => $resa['charge'],
          'place' => $api->place_get_by_id($resa['id_place'])['name'],
        ));
      }

      return $this->render('profile/history.html.twig', array(
            'resa_borne' => $resa_borne
      ));
    }
  }

 ?>
