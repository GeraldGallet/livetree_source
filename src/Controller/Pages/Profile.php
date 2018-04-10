<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Profile extends Controller
  {
    /**
      * @Route("/profil")
      */
    public function load_profile() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      $id_status = 2;
      $email = "gerald.gallet@isen.yncrea.fr";
      $phone_number = "0659605032";
      $facilities = ["Yncréa", "ICL"];
      $places = ["Parking P1", "Parking P2"];
      $car1 = array(
        'name' => "Voiture perso 1",
        'model' => "Tesla Model S",
        'power' => "20kWh"
      );
      $car2 = array(
        'name' => "Voiture de Madame",
        'model' => "Tesla Roadtser",
        'power' => "30kWh"
      );
      $cars = [$car1, $car2];

      return $this->render('profile/profile.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'id_status' => $id_status,
            'email' => $email,
            'phone_number' => $phone_number,
            'facilities' => $facilities,
            'places' => $places,
            'personal_cars' => $cars
      ));
    }
  }

?>
