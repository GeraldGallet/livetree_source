<?php
  namespace App\Controller\Pages;

  use App\Entity\ReservationCar;
  use App\Controller\CustomApi;

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

  class Cars extends Controller
  {
    /**
      * @Route("/voitures")
      */
    public function load_cars(Request $request) {
      session_start();

      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = 1;
      if($rights < 1)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $reason_chocies = [];
      $company_car_choices = [];
      $first = true;

      foreach($api->reason_get_all() as $reason) {
        $reason_choices[$reason['id_reason']] = $reason['id_reason'];
      }

      foreach($api->work_get($_SESSION['id_user']) as $work) {
        foreach($api->company_car_get_all($work['id_facility']) as $temp_car) {
          $company_car_choices[$temp_car['name']] = $temp_car['id_company_car'];
        }
      }

      $reservationCar = new ReservationCar();
      $car_form = NULL;
      $car_form = $this->createFormBuilder($reservationCar)
        ->add('id_company_car', ChoiceType::class, array('choices' => $company_car_choices, 'label' => "Voiture"))
        ->add('date_start', DateType::class, array('label' => "Date de départ"))
        ->add('start_time', TimeType::class, array('label' => "Heure de départ"))
        ->add('date_end', DateType::class, array('label' => "Date du retour"))
        ->add('end_time', TimeType::class, array('label' => "Heure du retour"))
        ->add('id_reason', ChoiceType::class, array('choices' => $reason_choices, 'label' => "Raison de l'emprunt"))
        ->add('reason_details', TextType::class, array('label' => "Détails si nécessaire"))
        ->add('km_planned', NumberType::class, array('label' => "Kilométrage prévu"))
        ->add('add_facility', SubmitType::class, array('label' => 'J\'enregistre ma réservation'))
        ->getForm();


      $car_form->handleRequest($request);


      if ($car_form->isSubmitted() && $car_form->isValid())
      {
    		date_default_timezone_set('Europe/Paris');
        $reservationCar = $car_form->getData();
        $new_resa = array(
          'date_start' => date_format($reservationCar->getDateStart(), 'Y-m-d'),
          'date_end' => date_format($reservationCar->getDateEnd(), 'Y-m-d'),
          'start_time' => $reservationCar->getStartTime()->format('H:i'),
          'end_time' => $reservationCar->getEndTime()->format('H:i'),
          'id_reason' => $reservationCar->getIdReason(),
          'reason_details' => $reservationCar->getReasonDetails(),
          'km_start' => NULL,
          'km_end' => NULL,
          'km_planned' => $reservationCar->getKmPlanned(),
          'id_user' => $_SESSION['id_user'],
          'id_company_car' => $reservationCar->getIdCompanyCar(),
          'id_state' => NULL
        );

        $api->reservation_car_add($new_resa);
      }

      return $this->render('reservations/cars.html.twig', array(
            'form' => $car_form->createView()
      ));
    }
  }

 ?>
