<?php

  namespace App\Controller\Pages;

  use App\Controller\CustomApi;
  use App\Entity\ReservationBorne;
  use \Datetime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\RangeType;
  use Symfony\Component\Form\Extension\Core\Type\TimeType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\DateType;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class Bornes extends Controller
  {

    /**
      * @Route("/bornes",name="bornes")
      */
    public function new(Request $request) {
      if(!isset($_SESSION))
        session_start();


      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

    	$reservationBorne = new ReservationBorne();
      $places_choice = [];
      $api = new CustomApi();

      foreach($api->table_get("has_access", array('id_user' => $_SESSION['id_user'])) as $acc) {
        $place_temp = $api->table_get("place", array('id_place' => $acc['id_place']))[0];
        $places_choice[$place_temp['name']] = $place_temp['id_place'];
      }

      $form = $this->createFormBuilder($reservationBorne)
        ->add('id_place', ChoiceType::class, array(
          'choices' => $places_choice,
          'label' => "Lieu"
        ))
  	    ->add('date_time',DateType::class, array(
        	'widget' => 'single_text',
        	'label' =>'Date: ',
          ))
  	    ->add('start_time', TimeType::class,array(
    		  'label' => "Heure d'arrivée: ",
    		  ))
    		->add('end_time', TimeType::class,array(
    		  'label' => "Heure de départ: ",
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
  	     ->add('subscribe', SubmitType::class, array('label' => 'Je réserve'))
  	     ->getForm();
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid())
  		{
    		date_default_timezone_set('Europe/Paris');
        $reservationBorne = $form->getData();

    		$inputDate = $reservationBorne->getDateTime();
    		$currentDate = new DateTime("now");
        $currentDate = $currentDate->format('Y:m:d');

    		if ($inputDate >= $currentDate) {
    			$inputStartTime = ($reservationBorne->getStartTime())->format('H:i');
          if($inputDate == $currentDate) {
            if($inputStartTime < (new DateTime("now"))->format('H:i')) {
              return $this->redirectToRoute('bornes');
            }
          }

    			$currentDate = (new DateTime("now"))->format('H:i');
    			$inputEndTime = ($reservationBorne->getEndTime())->format('H:i');
  				if($inputEndTime >= $inputStartTime) {
            $resa_borne = array(
              'date_resa' => date_format($inputDate, 'Y-m-d'),
              'start_time' => $inputStartTime,
              'end_time' => $inputEndTime,
              'charge' => $reservationBorne->getCharge(),
              'id_user' => $_SESSION['id_user'],
              'id_place' => $reservationBorne->getIdPlace()
            );

            $api->table_add("resa_borne", $resa_borne);
            return $this->redirectToRoute('history');
  				}
    		}

        return $this->render('reservations/bornes.html.twig', array(
          'form' => $form->createView(),
        ));
      }

      return $this->render('reservations/bornes.html.twig', array(
          'form' => $form->createView(),
      ));
    }
  }

 ?>
