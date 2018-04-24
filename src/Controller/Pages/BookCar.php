<?php
  namespace App\Controller\Pages;

  use App\Entity\BookCarEntity;
  use App\Controller\CustomApi;
  use App\Entity\ShowPlanningEntity;
  use App\Controller\Form\AvailableTimeCar;
  use \DateTime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\RangeType;
  use Symfony\Component\Form\Extension\Core\Type\TimeType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\DateType;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Form\FormEvent;
  use Symfony\Component\Form\FormEvents;

  class BookCar extends Controller
  {
    /**
      * @Route("/voitures")
      */
    public function load_cars(Request $request) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 1)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $reason_chocies = [];
      $company_car_choices = [];
      $planning = [];
      $first = true;

      foreach($api->table_get("work", array('id_user' => $_SESSION['id_user'])) as $work) {
        foreach($api->table_get("company_car", array('id_facility' => $work['id_facility'])) as $temp_car) {
          $company_car_choices[$temp_car['name'] . " (" . $temp_car['model'] . ")"] = $temp_car['id_company_car'];
        }
      }

      if(sizeof($company_car_choices) == 0) {
        return $this->render('reservations/cars.html.twig', array(
            'rights' => $_SESSION['rights'],
              'error' => "Le(s) établissement(s) au(x)quel(s) vous êtes affilié ne dispose(nt) pas de véhicule !",
              'success' => false
        ));
      }

      foreach($api->table_get_all("reason") as $reason) {
        $reason_choices[$reason['id_reason']] = $reason['id_reason'];
      }


      $reservationCar = new BookCarEntity();
      $car_form = NULL;
      $car_form = $this->get("form.factory")->createNamedBuilder('car_form', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', $reservationCar, array())
        ->add('id_company_car', ChoiceType::class, array('choices' => $company_car_choices, 'label' => "Voiture"))
        ->add('date_start', DateType::class, array('label' => "Date de départ", 'widget' => 'single_text'))
        ->add('start_time', TimeType::class, array('label' => "Heure de départ"))
        ->add('date_end', DateType::class, array('label' => "Date du retour", 'widget' => 'single_text'))
        ->add('end_time', TimeType::class, array('label' => "Heure du retour"))
        ->add('id_reason', ChoiceType::class, array('choices' => $reason_choices, 'label' => "Raison de l'emprunt"))
        ->add('reason_details', TextType::class, array('label' => "Détails si nécessaire"))
        ->add('km_planned', NumberType::class, array('label' => "Kilométrage prévu"))
        ->add('add_facility', SubmitType::class, array('label' => 'J\'enregistre ma réservation'))
        ->getForm();

      $obj_planning = new ShowPlanningEntity();
      $form_planning = $this->get("form.factory")->createNamedBuilder('planning_form')
       ->add('id_company_car_planning', ChoiceType::class, array(
         'choices' => $company_car_choices,
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
        $car_form->handleRequest($request);

        if($request->request->has('planning_form') && $form_planning->isValid()) {
          $res = $form_planning->getData();

          $start_date = $res['date_start'];
          $end_date = $res['date_end'];
          $temp_end_date = clone $end_date;
          $temp_end_date->modify("-7 day");

          if($start_date->format('Y:m:d') <= $end_date->format('Y:m:d') && $temp_end_date->format('Y:m:d') <= $start_date->format('Y:m:d')) {
            $planning_start = new DateTime();
            $planning_start->setDate($start_date->format('Y'), $start_date->format('m'), $start_date->format('d'));
            $planning_start->setTime(0, 0, 0);

            $planning_end = new DateTime();
            $planning_end->setDate($end_date->format('Y'), $end_date->format('m'), $end_date->format('d'));
            $planning_end->setTime(0, 0, 0);

            $plan = new AvailableTimeCar();
            $res = $plan->get_timeslots_with_CarId($res['id_company_car_planning'], array('end_date' => $planning_end, 'start_date' => $planning_start));
            $res = $plan->humanize_arrays($res);
            $planning = $this->humanize_planning($res);
          }
        }

        if($request->request->has('car_form') && $car_form->isValid()) {
          date_default_timezone_set('Europe/Paris');
          $reservationCar = $car_form->getData();

          $date_start = date_format($reservationCar->getDateStart(), 'Y-m-d');
          $date_end = date_format($reservationCar->getDateEnd(), 'Y-m-d');
          $start_time = $reservationCar->getStartTime()->format('H:i');
          $end_time = $reservationCar->getEndTime()->format('H:i');

          if($date_start > $date_end) {
            return $this->render('reservations/cars.html.twig', array(
                  'form' => $car_form->createView(),
                  'rights' => $_SESSION['rights'],
                  'success' => true,
                  'planning_form' => $form_planning->createView(),
                  'error_booking' => "Les dates ne correspondent pas !"
            ));
          } else if($date_start == $date_end) {
            if($start_time >= $end_time) {
              return $this->render('reservations/cars.html.twig', array(
                    'form' => $car_form->createView(),
                    'rights' => $_SESSION['rights'],
                    'success' => true,
                    'planning_form' => $form_planning->createView(),
                    'error_booking' => "Les horaires ne correspondent pas !"
              ));
            }
          }

          $new_resa = array(
            'date_start' => $date_start,
            'date_end' => $date_end,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'id_reason' => $reservationCar->getIdReason(),
            'reason_details' => $reservationCar->getReasonDetails(),
            'km_start' => NULL,
            'km_end' => NULL,
            'km_planned' => $reservationCar->getKmPlanned(),
            'id_user' => $_SESSION['id_user'],
            'id_company_car' => $reservationCar->getIdCompanyCar(),
            'id_state' => NULL
          );

          $new_state = array(
            'front' => null,
            'back' => null,
            'left_side' => null,
            'right_side' => null,
            'inside' => null,
            'commentary' => null,
            'id_resa' => null
          );

          $plan = new AvailableTimeCar();
          $planning_start = new DateTime();
          $planning_start->setDate(
            intval($reservationCar->getDateStart()->format('Y')),
            intval($reservationCar->getDateStart()->format('m')),
            intval($reservationCar->getDateStart()->format('d'))
          );
          $planning_start->setTime(
              intval($reservationCar->getStartTime()->format('H')),
              intval($reservationCar->getStartTime()->format('i')),
              intval($reservationCar->getStartTime()->format('s'))
          );

          $planning_end = new DateTime();
          $planning_end->setDate(
            intval($reservationCar->getDateEnd()->format('Y')),
            intval($reservationCar->getDateEnd()->format('m')),
            intval($reservationCar->getDateEnd()->format('d'))
          );
          $planning_end->setTime(
              intval($reservationCar->getEndTime()->format('H')),
              intval($reservationCar->getEndTime()->format('i')),
              intval($reservationCar->getEndTime()->format('s'))
          );

          $res = $plan->get_timeslots_with_CarId($reservationCar->getIdCompanyCar(), array('end_date' => $planning_end, 'start_date' => $planning_start), false);
          if($res['reservationAllowed']) {
            $new_resa['id_state'] = $api->table_add("state", $new_state);
            $id_resa_car = $api->table_add("resa_car", $new_resa);
            $api->table_update("state", array('id_resa' => $id_resa_car), array('id_state' => $new_resa['id_state']));
            return $this->redirectToRoute('history');
          } else {
            return $this->render('reservations/cars.html.twig', array(
                  'form' => $car_form->createView(),
                  'rights' => $_SESSION['rights'],
                  'success' => true,
                  'planning_form' => $form_planning->createView(),
                  'planning' => $planning,
                  'error_booking' => "Ce créneau n'est pas disponible ! Pensez à vérifier le planning"
            ));
          }
        }
      }

      return $this->render('reservations/cars.html.twig', array(
            'form' => $car_form->createView(),
            'rights' => $_SESSION['rights'],
            'success' => true,
            'planning_form' => $form_planning->createView(),
            'planning' => $planning,
            'error_booking' => null
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
      foreach($planning as $day) {
        foreach($day['plan'] as $creneau) {
          $begin_date = date_create_from_format('D. d F H:i:s', $day['day'] . ' ' . $creneau['begin']);
          $end_date = date_create_from_format('D. d F H:i:s', $day['day'] . ' ' . $creneau['end']);

          if(($start_time > $begin_date && $start_time < $end_date) || ($end_time > $begin_date && $end_time < $end_date)) {
            if(!$creneau['dispo'])
              return false;
          }
        }
      }
      return true;
    }
  }

 ?>
