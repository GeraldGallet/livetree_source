<?php
  namespace App\Controller\Pages;

  use App\Controller\CustomApi;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class History extends Controller
  {
    /**
      * @Route("/profil/historique", name="history")
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
          'id_resa' => $resa['id_resa'],
          'date_resa' => substr($resa['date_resa'], 0, 10),
          'start_time' => substr($resa['start_time'], 0, 5),
          'end_time' => substr($resa['end_time'], 0, 5),
          'charge' => $resa['charge'],
          'place' => $api->place_get_by_id($resa['id_place'])['name'],
        ));
      }

      foreach($api->reservation_car_get_by_user($_SESSION['id_user']) as $resa) {
        array_push($resa_car, array(
          'id_resa' => $resa['id_resa'],
          'date_start' => substr($resa['date_start'], 0, 10),
          'date_end' => substr($resa['date_end'], 0, 10),
          'start_time' => substr($resa['start_time'], 0, 5),
          'end_time' => substr($resa['end_time'], 0, 5),
          'km_planned' => $resa['km_planned'],
          'id_reason' => $resa['id_reason'],
          'id_company_car' => $resa['id_company_car'],
          'facility' => $api->facility_get_by_id(($api->company_car_get_by_id($resa['id_company_car'])['id_facility']))['name']
        ));
      }

      return $this->render('profile/history.html.twig', array(
            'resa_borne' => $resa_borne,
            'resa_car' => $resa_car
      ));
    }

    /**
      * @Route("/profil/delete_resa_borne/{id_resa}", name="delete_resa_borne")
      */
    public function delete_resa_borne($id_resa) {
      session_start();
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $resa = $api->reservation_borne_get_by_id($id_resa);
      if($_SESSION['id_user'] != $resa['id_user'])
        return $this->redirectToRoute('accueil');

      $api->reservation_borne_delete($id_resa);
      return $this->redirectToRoute('history');
    }

    /**
      * @Route("/profil/delete_resa_car/{id_resa}", name="delete_resa_car")
      */
    public function delete_resa_car($id_resa) {
      session_start();
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $resa = $api->reservation_car_get_by_id($id_resa);
      if($_SESSION['id_user'] != $resa['id_user'])
        return $this->redirectToRoute('accueil');

      $api->reservation_car_delete($id_resa);
      return $this->redirectToRoute('history');
    }
  }

 ?>
