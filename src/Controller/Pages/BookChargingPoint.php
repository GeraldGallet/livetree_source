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

  class BookChargingPoint extends Controller
  {

    /**
      * @Route("/bornes",name="bornes")
      */
    public function new(Request $request) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      date_default_timezone_set('Europe/Paris');
      $api = new CustomApi();
      $res = $api->table_get("has_access", array('id_user' => $_SESSION['id_user']));
      if(sizeof($res) == 0) {
        return $this->render('reservations/bornes.html.twig', array(
          'success' => false,
          'error' => "Vous n'avez accès à aucun lieu ! Rendez-vous dans la section profil pour renseigner les lieux auquels vous avez accès. Si vous n'avez aucun accès, demandez à votre établissement comment en obtenir",
          'rights' => $_SESSION['rights']
        ));
      } else {
        $place_choices = [];
        foreach($res as $acc) {
          $place_temp = $api->table_get("place", array('id_place' => $acc['id_place']))[0];
          $places_choice[$place_temp['name']] = $place_temp['id_place'];
        }
      }

      $res = $api->table_get("personal_car", array('id_user' => $_SESSION['id_user']));
      if(sizeof($res) == 0) {
        return $this->render('reservations/bornes.html.twig', array(
          'success' => false,
          'error' => "Vous n'avez aucune voiture enregistrée ! Rendez-vous dans la section profil pour enregistrer une voiture afin de pouvoir réserver une borne de recharge !",
          'rights' => $_SESSION['rights']
        ));
      } else {
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

          if($start_date->format('Y:m:d') < $end_date->format('Y:m:d') && $temp_end_date->format('Y:m:d') <= $start_date->format('Y:m:d')) {
            $planning_start = new DateTime();
            $planning_start->setDate($start_date->format('Y'), $start_date->format('m'), $start_date->format('d'));
            $planning_start->setTime(0, 0, 0);

            $planning_end = new DateTime();
            $planning_end->setDate($end_date->format('Y'), $end_date->format('m'), $end_date->format('d'));
            $planning_end->setTime(0, 0, 0);

            $plan = new AvailableTime();
            $res = $plan->get_timeslots_with_placeId($res->getIdPlacePlanning(), array('end_date' => $planning_end, 'start_date' => $planning_start));
            dump($planning_start);
            dump($end_date);
            dump($res);
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

                $api->table_add("resa_borne", $resa_borne);
                return $this->redirectToRoute('history');
              }
		        }
    		  }
        }
      }

      return $this->render('reservations/bornes.html.twig', array(
        'success' => true,
        'form' => $form->createView(),
        'form_planning' => $form_planning->createView(),
        'rights' => $_SESSION['rights']
      ));
  }
}

?>
