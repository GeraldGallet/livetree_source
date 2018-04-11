<?php
  namespace App\Controller;

  use App\Controller\CustomApi;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class test_api extends Controller {
    /**
      * @Route("/test_api", name="test")
      */
    function test() {
      $mail = 'robin.poiret@isen.yncrea.fr';
      $pass1 = "passw0rdR";
      $pass2 = "hell0g00dpass";

      echo "Hello TEST API ;)\n";
      // GET EXAMPLE
      ///*
      $api_interface = new CustomApi();
      //var_dump($api_interface->user_get($mail));
      //$api_interface->user_delete($mail);
      $user = array(
        'email' => 'robin.poiret@isen.yncrea.fr',
        'first_name' => 'Robin',
        'last_name' => 'Poiret',
        'password' => 'passw0rdR',
        'phone_number' => '0606060606',
        'id_status' => 'student'
      );
      //$api_interface->user_add($user);
      //$api_interface->user_change_password($mail, $pass2);

      $status = array(
        'id_status' => 'teacher',
        'rights' => 2
      );
      //$api_interface->status_add($status);
      //$api_interface->status_delete("teacher");
      //var_dump($api_interface->status_get("guest"));

      $car = array(
        'model' => "Renault Kangoo",
        'power' => 30,
        'name' => "Voiture perso 2",
        'id_user' => 1
      );
      $user = array(
        'id_user' => 1,
      );
      //$api_interface->personal_car_add($car);
      //var_dump($api_interface->personal_car_get_all($user));
      //var_dump($api_interface->personal_car_get(1, "Voiture perso"));
      //$api_interface->personal_car_delete(1, "Voiture perso 2")

      $facility = array(
        'name' => "Yncréa",
        'address' => "29 Boulevard Vauban, 59800 Lille",
        'complementary' => "RAS"
      );
      //$api_interface->facility_add($facility);
      //var_dump($api_interface->facility_get_all());
      //var_dump($api_interface->facility_get("Yncréa"));
      //$api_interface->facility_delete("Yncréa");
      $result = NULL;
      //$result = $api_interface->place_get_all();

      $car = array(
        'model' => "Renault Kangoo",
        'power' => 30,
        'name' => "Voiture #2",
        'id_facility' => 2
      );
      $id_facility = 2;
      //$result = $api_interface->company_car_get_all(2);
      //$api_interface->company_car_add($car);
      //$api_interface->company_car_delete(2);
      //var_dump($api_interface->personal_car_get_all($user));
      //var_dump($api_interface->personal_car_get(1, "Voiture perso"));
      //$api_interface->personal_car_delete(1, "Voiture perso 2")

      $borne = array(
        'name' => "Borne #2",
        'place' => "2e étage",
        'id_place' => 1
      );

      //$result = $api_interface->borne_get(1);
      //$api_interface->borne_add($borne);
      //$api_interface->borne_delete(2);
      return $this->render('test.html.twig', array(
            'result' => $result,
      ));
    }
  }
?>
