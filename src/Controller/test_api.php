<?php
  namespace App\Controller;

  use App\Controller\CustomApi;
  use \DateTime;

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

      //$result = $api_interface->work_get_all();
      //$result = $api_interface->work_get(3);
      //$api_interface->work_add(3, 2);
      //$api_interface->work_delete(3, 3);

      //$result = $api_interface->domain_get_all();
      //$result = $api_interface->domain_get("hei.fr");
      //$api_interface->domain_delete("hei.fr");

      //$result = $api_interface->has_domain_get_all();
      //$result = $api_interface->has_domain_get(2);
      //$api_interface->has_domain_delete(1, 2);

      /*
      $domain_name = substr(strrchr("gerald.gallet@yncrea.fr", "@"), 1);
      $res = $api_interface->domain_get($domain_name);
      $id_domain = $api_interface->domain_get($domain_name)[0]['id_domain'];

      $id_facs = [];
      foreach($api_interface->has_domain_get($res[0]['id_domain']) as $has_domain) {
        array_push($id_facs, $has_domain['id_facility']);
      }
      */

      //$result = $api_interface->phone_indicative_get_all();
      //$result = $api_interface->phone_indicative_get("+33");
      //$api_interface->phone_indicative_add("+34", "Allemagne");
      //$api_interface->phone_indicative_delete("+34");

      //$api_interface->has_access_add(3, 1);
      //$result = $api_interface->has_access_get(3);
      //$api_interface->has_access_delete(3, 1);
      //$date = new DateTime('2018-04-15 17:00:00');
      //$date2 = new DateTime('2018-04-15 19:00:00');
      // /$api_interface->reservation_borne_add(date_format($date, 'Y-m-d'), date_format($date, 'H:i:s'), date_format($date2, 'H:i:s'), 50, 3, 1);

      //$result = $api_interface->reason_get("Visite");
      //$api_interface->reason_delete("Personnel");

      $user = array(
        'first_name' => "Gallet",
        'email' => "gerald.gallet@yncrea.fr"
      );
      //$result = $api_interface->test_user_get($user);
      $car = array(
        'model' => "Renault Kangoo"
      );
      $where = array(
        'id_user' => 1,
      );

      $set = array('password' => "yoyoyoHIHI");
      //$result = $api_interface->table_update("user", $set, $where);



      //$result = $api_interface->table_get("resa_borne", $criteres);



      $api_interface = new CustomApi();
      //$result = $api_interface->table_get_all("user");


      $mail_body = array(
        'email' => 'gerald.gallet@isen.yncrea.fr',
        'subject' => "Validation de votre compte",
        'html' => "<p>Vous pouvez valider votre compte Live Tree en cliquant sur ce lien</p>"
      );
      //$api_interface->send_mail($mail_body);

      //$result = $api_interface->custom_request("SELECT * FROM facility");
      dump($api_interface->table_get("resa_car", array('id_resa' => 13)));
      return $this->render('test.html.twig', array(
            'result' => $result,
      ));
    }
  }
?>
