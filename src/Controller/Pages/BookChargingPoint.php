<?php

  namespace App\Controller\Pages;

  use App\Controller\CustomApi;
  use App\Entity\BookChargingPointEntity;
  use App\Entity\ShowPlanningEntity;
  use App\Controller\Form\AvailableTime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use \DateTime;

  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
  use Symfony\Component\Form\Extension\Core\Type\DateType;
  use Symfony\Component\Form\Extension\Core\Type\TimeType;
  use Symfony\Component\Form\Extension\Core\Type\RangeType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//Classe controlant la réservation de Borne
  class BookChargingPoint extends Controller
  {

    /**
      * @Route("/bornes",name="bornes")
      */
    public function new(Request $request) {
      //On vérifie si l'utilisateur est connecté
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      date_default_timezone_set('Europe/Paris');
      $api = new CustomApi();//L'interface pour l'API
      $planning = null;//Planing accessible par l'utilisateur
      $error_booking = null;//erreur en cas de réservain
      $res = $api->table_get("has_access", array('id_user' => $_SESSION['id_user']));//On vérifie si l'utilisateur a renseigné un ou des accès à des lieux
      if(sizeof($res) == 0) {//Si il n'a rien renseigné on lui informe que pour réserver une borne i doit avoir accès à au moins un lieu
        return $this->render('reservations/bornes.html.twig', array(
          'success' => false,
          'error' => "Vous n'avez accès à aucun lieu ! Rendez-vous dans la section profil pour renseigner les lieux auquels vous avez accès. Si vous n'avez aucun accès, demandez à votre établissement comment en obtenir",
          'rights' => $_SESSION['rights'],
          'planning' => $planning,
          'error_booking' => $error_booking
        ));
      } else {
        $place_choices = [];//Sinon on affiche les choix des diffèrents lieux de l'utilisateur
        foreach($res as $acc) {
          $place_temp = $api->table_get("place", array('id_place' => $acc['id_place']))[0];
          $places_choice[$place_temp['name']] = $place_temp['id_place'];
        }
      }

      $res = $api->table_get("personal_car", array('id_user' => $_SESSION['id_user']));//On vérifie si l'utilisaeur a renseigné un ou des véhicules
      if(sizeof($res) == 0) {//Si aucun véhicule n'est renseigné on lui demande d'en ajouter un pour pouvoir reéserver une borne
        return $this->render('reservations/bornes.html.twig', array(
          'success' => false,
          'error' => "Vous n'avez aucune voiture enregistrée ! Rendez-vous dans la section profil pour enregistrer une voiture afin de pouvoir réserver une borne de recharge !",
          'rights' => $_SESSION['rights'],
          'planning' => $planning,
          'error_booking' => $error_booking
        ));
      } else {//Sinon on affiche les différents véhicules de l'utilisateur
        $personal_car_choices = [];
        foreach($res as $pc) {
          $personal_car_choices[$pc['name']] = $pc['id_personal_car'];
        }
      }

      $obj = new BookChargingPointEntity();
      $form = $this->get("form.factory")->createNamedBuilder('form', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', $obj, array())
        ->add('id_place', ChoiceType::class, array(
          'choices' => $places_choice,
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
         ->add('id_personal_car', ChoiceType::class, array(
           'choices' => $personal_car_choices,
           'label' => "Voiture à recharger: "
         ))
  	     ->add('subscribe', SubmitType::class, array('label' => 'Je réserve'))
  	     ->getForm();

      $obj_planning = new ShowPlanningEntity();
      $form_planning = $this->get("form.factory")->createNamedBuilder('planning_form', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', $obj_planning, array())
       ->add('id_place_planning', ChoiceType::class, array(
         'choices' => $places_choice,
         'label' => "Lieu: "
       ))
       ->add('date_start', DateType::class,array(
         'label' => "Début: ",
         'widget' => 'single_text'
       ))
       ->add('date_end', DateType::class,array(
         'label' => "Fin: ",
         'widget' => 'single_text'
       ))
        ->add('show_planning', SubmitType::class, array('label' => 'Je consulte le planning'))
        ->getForm();

      if('POST' === $request->getMethod()) {
        $form_planning->handleRequest($request);
        $form->handleRequest($request);

        if($request->request->has('planning_form') && $form_planning->isValid()) {
          $res = $form_planning->getData();

          $start_date = $obj_planning->getDateStart();
          $end_date = $obj_planning->getDateEnd();
          $temp_end_date = clone $end_date;
          $temp_end_date->modify("-7 day");

          if($start_date->format('Y:m:d') <= $end_date->format('Y:m:d') && $temp_end_date->format('Y:m:d') <= $start_date->format('Y:m:d')) {
            $planning_start = new DateTime();
            $planning_start->setDate($start_date->format('Y'), $start_date->format('m'), $start_date->format('d'));
            $planning_start->setTime(0, 0, 0);

            $planning_end = new DateTime();
            $planning_end->setDate($end_date->format('Y'), $end_date->format('m'), $end_date->format('d'));
            $planning_end->setTime(0, 0, 0);

            $plan = new AvailableTime();
            $res = $plan->get_timeslots_with_placeId($res->getIdPlacePlanning(), array('end_date' => $planning_end, 'start_date' => $planning_start));
            //dump($res);
            $res = $plan->humanize_arrays($res);
            $planning = $this->humanize_planning($res);
          }
        }

        if($request->request->has('form') && $form->isValid()) {
          $obj = $form->getData();

      		$start_date = $obj->getStartDate();
      		$current_date = new DateTime("now");
          $current_date = $current_date->format('Y:m:d');

      		if($start_date->format('Y:m:d') > $current_date) {
    		    $input_start_time = ($obj->getStartDate())->format('H:i');
    			  $input_end_time = ($obj->getEndDate())->format('H:i');

            if($input_end_time >= $input_start_time) {
              if($obj->getCharge() < 100) {
                $end_date = new DateTime();
                $end_date->setDate($start_date->format('Y'), $start_date->format('m'), $start_date->format('d'));
                $end_date->setTime($obj->getEndDate()->format('H'), $obj->getEndDate()->format('i'), $obj->getEndDate()->format('s'));

                $plan = new AvailableTime();
                $resa_borne = array(
                  'date_creation' => date_format(new DateTime("now"),'Y:m:d'),
                  'start_date' => date_format($start_date, 'Y-m-d H:i:s'),
                  'end_date' => date_format($end_date, 'Y-m-d H:i:s'),
                  'date_last_modification' => $current_date,
                  'charge' => $obj->getCharge(),
                  'id_user' => $_SESSION['id_user'],
                  'id_place' => $obj->getIdPlace(),
                  'id_personal_car' => $obj->getidPersonalCar()
                );
                $res = $plan->get_timeslots_with_placeId($obj->getIdPlace(), array('end_date' => $end_date, 'start_date' => $start_date));
                $res = $plan->humanize_arrays($res);
                $planning = $this->humanize_planning($res);
                $available = $this->is_available($start_date, $end_date, $planning[0]);
                if($available) {
                  $id = $api->table_add("resa_borne", $resa_borne);
                  return $this->redirectToRoute('history');
                } else
                  $error_booking = "Ce créneau n'est pas libre ! Vérifiez le planning avant de réserver";


              }
		        }
    		  }
        }
      }

      return $this->render('reservations/bornes.html.twig', array(
        'success' => true,
        'form' => $form->createView(),
        'form_planning' => $form_planning->createView(),
        'rights' => $_SESSION['rights'],
        'planning' => $planning,
        'error_booking' => $error_booking
      ));
  }

  private function humanize_planning($res) {
    $max_available = $res['numberMaxOfBookingPerParking'];
    $list = $res['updatedListeOfBooking'];
    $planning = array();
    foreach($list as $day) {
      $day_planning = [];
      $readable_date = $day[0]['date']->format('D. d F');
      $old_var = $day[0]['numberOfDisponibility'];
      if($max_available - $old_var > 0) {
        $dispo = true;
        $old_var = true;
      } else {
        $dispo = false;
        $old_var = false;
      }
      $creneau = array(
        'begin' => date_format($day[0]['date'], 'H:i:s'),
        'dispo' => $dispo
      );
      for($i = 1; $i < sizeof($day); $i++) {
        if($max_available - $day[$i]['numberOfDisponibility'] > 0) {
          $actual_var = true;
          $dispo = true;
        } else {
          $actual_var = false;
          $dispo = false;
        }

        if($old_var != $actual_var) {
          $creneau['end'] = date_format($day[$i]['date'], 'H:i:s');
          array_push($day_planning, $creneau);
          $old_var = $actual_var;
          $creneau = array(
            'begin' => date_format($day[$i]['date'], 'H:i:s'),
            'dispo' => $dispo
          );
        }
      }
      $creneau['end'] = date_format($day[sizeof($day)-1]['date'], 'H:i:s');
      array_push($day_planning, $creneau);
      array_push($planning, array(
        'day' => $readable_date,
        'plan' => $day_planning
      ));
    }

    return $planning;
  }

  private function is_available($start_time, $end_time, $planning) {
    foreach($planning['plan'] as $creneau) {
      $begin_date = date_create_from_format('D. d F H:i:s', $planning['day'] . ' ' . $creneau['begin']);
      $end_date = date_create_from_format('D. d F H:i:s', $planning['day'] . ' ' . $creneau['end']);

      if(($start_time > $begin_date && $start_time < $end_date) || ($end_time > $begin_date && $end_time < $end_date)) {
        if(!$creneau['dispo'])
          return false;
      }
    }
    return true;
  }
}

?>
