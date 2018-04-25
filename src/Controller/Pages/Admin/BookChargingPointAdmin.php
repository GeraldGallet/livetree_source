<?php
  namespace App\Controller\Pages\Admin;

  use App\Entity\BookChargingPointEntity;
  use App\Entity\FiltreReservationBorne;
  use App\Controller\CustomApi;
  use \DateTime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Form\Extension\Core\Type\TimeType;
  use Symfony\Component\Form\Extension\Core\Type\DateType;
  use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
    use Symfony\Component\Form\Extension\Core\Type\RangeType;


  class BookChargingPointAdmin extends Controller
  {
    /**
      * @Route("/admin/bornes", name="admin_bornes")
      */
    public function load_admin_bornes(Request $request) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $place_choices = [];
      $resas = [];
      $user_choice = [];

      $place_options = "AND (";
      if($rights == 3) {
        foreach($api->table_get_all("place") as $place) {
          $place_choices[$place['name']] = $place['id_place'];
          $place_options .= 'id_place = ' . $place['id_place'] . " OR ";
        }
      } else {
        foreach($api->table_get("has_access", array('id_user' => $_SESSION['id_user'])) as $acc) {
          $place_choices[$api->table_get("place", array('id_place' => $acc['id_place']))[0]['name']] = $acc['id_place'];
          $place_options .= 'id_place = ' . $acc['id_place'] . " OR ";
        }
      }
      $place_options .= "1)";

      foreach($place_choices as $id) {
        foreach($api->table_get("has_access", array('id_place' => $id)) as $acc) {
          $user = $api->table_get("user", array('id_user' => $acc['id_user']))[0];
          $user_choice[$user['email']] = $user['id_user'];
        }
      }

      $_SESSION['total_charging_points'] = sizeof($api->table_get("resa_borne", array(), [$place_options]));

      $resas = [];
      $options = [$place_options, "ORDER BY start_date DESC ", "LIMIT " . $_SESSION['limit_charging_points'] . " ", "OFFSET " . $_SESSION['offset_charging_points'] . " "];
      $res = $api->table_get("resa_borne", array(), $options);
      $actual_max = sizeof($res) + $_SESSION['offset_charging_points'];
      foreach($res as $resa) {
        array_push($resas, array(
          'start_date' => $resa['start_date'],
          'end_date' => $resa['end_date'],
          'charge' => $resa['charge'],
          'id_place' => $api->table_get("place", array('id_place' => $resa['id_place']))[0]['name'],
          'id_user' => $api->table_get("user", array('id_user' => $resa['id_user']))[0]['email'],
          'id_resa' => $resa['id_resa']
        ));
      }

      $resa_borne = new BookChargingPointEntity();
      $resa_form = $this->get("form.factory")->createNamedBuilder('adding_form', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', $resa_borne, array())
        ->add('id_place', ChoiceType::class, array(
          'choices' => $place_choices,
          'label' => "Lieu: "
        ))
        ->add('start_date', DateTimeType::class,array(
          'label' => "Date et heure d'arrivée: ",
          'date_widget' => 'single_text',
          'widget' => 'choice',
          'minutes' => [0, 15, 30, 45]
        ))
        ->add('end_date', TimeType::class,array(
          'label' => "Heure de départ: ",
          'widget' => 'choice',
          'minutes' => [0, 15, 30, 45]
        ))
        ->add('charge', RangeType::class, [
                   'attr' => [
                      "data-provide" => "slider",
                      "data-slider-ticks" => "[1, 2, 3, 4]",
                      "data-slider-ticks-labels" => '["short", "medium", "long", "xxl"]',
                      "min" => 1,
                      "max" => 100,
                      "step" => 1,
                      "value" => 100,
                   ]
               ]
         )
         ->add('id_user', ChoiceType::class, array(
           'label' => "Utilisateur: ",
           'choices' => $user_choice
         ))
         ->add('id_personal_car', ChoiceType::class, array(
           'choices' => array('2' => 2),
           'label' => "Voiture à recharger: "
         ))
         ->add('subscribe', SubmitType::class, array(
           'label' => 'Je réserve',
           'attr' => [
             'class' => "btn btn-outline-secondary",
             ]))
         ->getForm();

       $limit_form = $this->get("form.factory")->createNamedBuilder('change_limit_form')
         ->add('new_limit', NumberType::class, array(
           'label' => false,
           'attr' => array(
             'maxlength' => '5',
             'size' => '5'
           )
         ))
         ->add('subscribe_change', SubmitType::class, array(
           'label' => 'Changer le nombre de réservations par page',
           'attr' => [
             'class' => "btn btn-outline-secondary",
             ]))
         ->getForm();

       $go_to_form = $this->get("form.factory")->createNamedBuilder('go_to_form')
         ->add('number', NumberType::class, array(
           'label' => false,
           'attr' => array(
             'maxlength' => '5',
             'size' => '5'
           )
         ))
         ->add('subscribe_go_to', SubmitType::class, array(
           'label' => 'Aller directement à cette réservation',
           'attr' => [
             'class' => "btn btn-outline-secondary",
             ]))
         ->getForm();

      if('POST' === $request->getMethod()) {
        $resa_form->handleRequest($request);
        $limit_form->handleRequest($request);
        $go_to_form->handleRequest($request);

        if($request->request->has('change_limit_form') && $limit_form->isValid()) {
          if($limit_form->getData()['new_limit'] > 0)
            $_SESSION['limit_charging_points'] = $limit_form->getData()['new_limit'];
          return $this->redirectToRoute('admin_cars');
        }

        if($request->request->has('go_to_form') && $go_to_form->isValid()) {
          if($go_to_form->getData()['number'] <= $_SESSION['total_charging_points']) {
            $_SESSION['offset_charging_points'] = $go_to_form->getData()['number']-1;
          }
          return $this->redirectToRoute('admin_cars');
        }

        if($request->request->has('adding_form') && $resa_form->isValid()) {
          $resa_borne = $resa_form->getData();
          $start_date = $resa_borne->getStartDate();
          $end_date = new DateTime();
          $end_date->setDate($start_date->format('Y'), $start_date->format('m'), $start_date->format('d'));
          $end_date->setTime($resa_borne->getEndDate()->format('H'), $resa_borne->getEndDate()->format('i'), $resa_borne->getEndDate()->format('s'));

          $api->table_add("resa_borne", array(
            'date_creation' => date_format(new DateTime("now"), 'Y-m-d'),
            'date_last_modification' => date_format(new DateTime("now"), 'Y-m-d'),
            'start_date' => $start_date->format('Y-m-d H:i:s'),
            'end_date' => $end_date->format('Y-m-d H:i:s'),
            'charge' => $resa_borne->getCharge(),
            'id_user' => $resa_borne->getIdUser(),
            'id_place' => $resa_borne->getIdPlace(),
            'id_personal_car' => $resa_borne->getIdPersonalCar()
          ));

          return $this->redirectToRoute('admin_bornes');
        }
      }

      if($_SESSION['total_charging_points'] == 0)
        $actual_min = 0;
      else
        $actual_min = $_SESSION['offset_charging_points']+1;

      return $this->render('admin/admin_bornes.html.twig', array(
            'resa_bornes' => $resas,
            'resa_form' => $resa_form->createView(),
            'limit_form' => $limit_form->createView(),
            'go_to_form' => $go_to_form->createView(),
            'rights' => $_SESSION['rights'],
            'limit' => $_SESSION['limit_charging_points'],
            'offset' => $_SESSION['offset_charging_points'],
            'actual_min' => $actual_min,
            'actual_max' => $actual_max,
            'total' => $_SESSION['total_charging_points']
      ));
    }

    /**
     * @Route("/admin/bornes/delete/{id_resa}", name="delete_resa_borne_admin")
     */
    public function delete_resa($id_resa) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = 2;
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $present = false;
      $api = new CustomApi();
      if($rights == 2) {
        $resa = $api->table_get("resa_borne", array('id_resa' => $id_resa))[0];
        foreach($api->table_get("has_access", array('id_user' => $_SESSION['id_user'])) as $acc) {
          if($acc['id_place'] == $resa['id_place']) {
            $present = true;
            break;
          }
        }
      } else
        $present = true;

      if(!$present)
        return $this->redirectToRoute('accueil');

      $email = $api->table_get("user", array('id_user' => $resa['id_user']))[0]['email'];
      $mail_body = array(
        'email' => $email,
        'subject' => "Annulation de votre réservation n°" . $id_resa,
        'html' => "<p>Votre réservation de borne décrite ci-dessous a été supprimée par l'Administrateur " . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . "</p>
        <ul>
          <li>Début: " . $resa['start_date'] . "</li>
          <li>Fin: " . $resa['end_date'] . "</li>
          <li>Charge estimée en arrivant: " . $resa['charge'] . "</li>
          <li>Lieu: " . $api->table_get("place", array('id_place' => $resa['id_place']))[0]['name'] . "</li>
        <ul>
        <p>Vous pouvez le contacter à l'adresse <u>" . $_SESSION['email'] . "</u>.</p>
        "
      );

      $api->send_mail($mail_body);
      $api->table_delete("resa_borne", array('id_resa' => $id_resa));
      return $this->redirectToRoute('admin_bornes');
    }

    /**
      * @Route("/admin/bornes/show/{way}", name="change_offset_borne_admin")
      */
    public function change_offset($way) {
      if($way == 1 && ($_SESSION['limit_charging_points'] + $_SESSION['offset_charging_points']) < $_SESSION['total_charging_points']) {
        $_SESSION['offset_charging_points'] += $_SESSION['limit_charging_points'];
      } else if($way == -1 && $_SESSION['offset_cars'] != 0) {
        $_SESSION['offset_charging_points'] -= $_SESSION['limit_charging_points'];
      } else if($way == 0) {
        $_SESSION['offset_charging_points'] = 0;
      }
      return $this->redirectToRoute('admin_bornes');
    }

    /**
      * @Route("/admin/bornes/extract", name="extract_charging_points_admin")
      */
    public function extract_data() {
      $api = new CustomApi();
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $place_options = "AND (";
      if($rights == 3) {
        foreach($api->table_get_all("place") as $place) {
          $place_options .= 'id_place = ' . $place['id_place'] . " OR ";
        }
      } else {
        foreach($api->table_get("has_access", array('id_user' => $_SESSION['id_user'])) as $acc) {
          $place_options .= 'id_place = ' . $acc['id_place'] . " OR ";
        }
      }
      $place_options .= "1)";

      $resas = [];
      $options = [$place_options, "ORDER BY start_date DESC ", "LIMIT " . $_SESSION['limit_charging_points'] . " ", "OFFSET " . $_SESSION['offset_charging_points'] . " "];
      $res = $api->table_get("resa_borne", array(), $options);
      foreach($res as $resa) {
        array_push($resas, array(
          'start_date' => $resa['start_date'],
          'end_date' => $resa['end_date'],
          'charge' => $resa['charge'],
          'id_place' => $api->table_get("place", array('id_place' => $resa['id_place']))[0]['name'],
          'id_user' => $api->table_get("user", array('id_user' => $resa['id_user']))[0]['email'],
          'id_resa' => $resa['id_resa']
        ));
      }

      $fp = fopen('charging_points.json', 'w');
      fwrite($fp, json_encode($resas));
      fclose($fp);
      return $this->redirectToRoute('admin_bornes');
    }
  }

 ?>
