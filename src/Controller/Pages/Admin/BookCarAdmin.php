<?php
  namespace App\Controller\Pages\Admin;

  use App\Controller\CustomApi;
  use App\Entity\AdminReservationCar;
  use App\Entity\State;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Form\Extension\Core\Type\RangeType;
  use Symfony\Component\Form\Extension\Core\Type\TimeType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
  use Symfony\Component\Form\Extension\Core\Type\DateType;
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Routing\Annotation\Route;

  class BookCarAdmin extends Controller
  {
    /**
      * @Route("/admin/voitures", name="admin_cars")
      */
    public function load_admin_cars(Request $request) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $resa_car = [];
      $reason_choices = [];
      $company_car_choices = [];
      $api = new CustomApi();
      $state_of_form = new State();

      $facility_choices = [];
      if($rights == 2) {
        foreach($api->table_get("work", array('id_user' => $_SESSION['id_user'])) as $work)
          array_push($facility_choices, $work['id_facility']);
      } else {
        foreach($api->table_get_all("facility") as $fac)
          array_push($facility_choices, $fac['id_facility']);
      }

      foreach($api->table_get_all("reason") as $reason)
        $reason_choices[$reason['id_reason']] = $reason['id_reason'];

      $user_choice = [];
      foreach($facility_choices as $id) {
        foreach($api->table_get("work", array('id_facility' => $id)) as $work) {
          $user = $api->table_get("user", array('id_user' => $work['id_user']))[0];
          $user_choice[$user['email']] = $user['id_user'];
        }
      }

      foreach($api->table_get("work", array('id_user' => $_SESSION['id_user'])) as $work) {
        foreach($api->table_get("company_car", array('id_facility' => $work['id_facility'])) as $temp_car) {
          $company_car_choices[$temp_car['name']] = $temp_car['id_company_car'];
        }
      }

      $cco = "AND (";
      foreach($facility_choices as $id_fac) {
        foreach($api->table_get("company_car", array('id_facility' => $id_fac)) as $cc) {
          $cco .= 'id_company_car = ' . $cc['id_company_car'] . " OR ";
        }
      }
      $cco .= "1)";

      $options = [$cco, "ORDER BY date_start DESC "];
      $resa_car_db = $api->table_get("resa_car", array(), $options);
      if(sizeof($resa_car_db != 0)) {
        foreach($resa_car_db as $resa) {
          $car = $api->table_get("company_car", array('id_company_car' => $resa['id_company_car']))[0];
          if($resa['id_user'] == null) {
            $id_user = "Ce compte a été supprimé";
          } else {
            $res = $api->table_get("user", array('id_user' => $resa['id_user']))[0];
            $id_user = $res['first_name'] . " " . $res['last_name'] . " (" . $res['id_user'] . ")";
          }
          $temp_resa = array(
            'id_resa' => $resa['id_resa'],
            'date_start' => substr($resa['date_start'], 0, 10),
            'date_end' => substr($resa['date_end'], 0, 10),
            'start_time' => substr($resa['start_time'], 0, 5),
            'end_time' => substr($resa['end_time'], 0, 5),
            'km_planned' => $resa['km_planned'],
            'id_reason' => $resa['id_reason'],
            'id_company_car' => $car['name'] . " (" . $car['model'] . ")",
            'facility' => $api->table_get("facility", array('id_facility' => ($car ['id_facility'])))[0] ['name'],
            'id_user' => $id_user
          );

          $state = $api->table_get("state", array('id_state' => $resa['id_state']))[0];
          $temp_state = [];
          if($state['front'] == NULL) {
            $temp_state['done'] = false;
            $state_form = $this->createFormBuilder($state_of_form)
            ->add('front', CheckboxType::class, array(
                'label'    => 'Avant OK ? '
              ))
            ->add('back', CheckboxType::class, array(
                'label'    => 'Arrière OK ? '
              ))
            ->add('right_side', CheckboxType::class, array(
                'label'    => 'Droite OK ? '
              ))
            ->add('left_side', CheckboxType::class, array(
                'label'    => 'Gauche OK ? '
              ))
            ->add('inside', CheckboxType::class, array(
                'label'    => 'Intérieur OK ? '
              ))
            ->add('commentary', TextType::class, array('label' => "Commentaire (facultatif) "))
            ->add('id_state', HiddenType::class, array('data' => $resa['id_state']))
      	    ->add('confirm', SubmitType::class, array('label' => 'Je valide cet état des lieux'))
        	  ->getForm();
            $state_form->handleRequest($request);

            if($state_form->isSubmitted() && $state_form->isValid()) {
              $state_of_form = $state_form->getData();
              $set = array(
                'front' => $state_of_form->getFront(),
                'back' => $state_of_form->getBack(),
                'left_side' => $state_of_form->getLeftSide(),
                'right_side' => $state_of_form->getRightSide(),
                'inside' => $state_of_form->getInside(),
                'commentary' => $state_of_form->getCommentary(),
              );

              $api->table_update("state", $set, array('id_state' => $state_of_form->getIdState()));
              return $this->redirectToRoute('admin_cars');
            }

            $temp_state['form'] = $state_form->createView();
          } else {
            $temp_state = array(
              'done' => true,
              'front' => $state['front'],
              'back' => $state['back'],
              'left_side' => $state['left_side'],
              'right_side' => $state['right_side'],
              'inside' => $state['inside'],
              'commentary' => $state['commentary'],
              'form' => NULL
            );
          }
          $temp_resa['state'] = $temp_state;
          array_push($resa_car, $temp_resa);
        }
      }

      $reservationCar = new AdminReservationCar();
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
        ->add('id_user', ChoiceType::class, array(
          'label' => "Utilisateur",
          'choices' => $user_choice
        ))
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
          'id_user' => $reservationCar->getIdUser(),
          'id_company_car' => $reservationCar->getIdCompanyCar(),
          'id_state' => NULL
        );

        $api->table_add("resa_car", $new_resa);
      }

      return $this->render('admin/admin_cars.html.twig', array(
        'resa_car' => $resa_car,
        'car_form' => $car_form->createView(),
        'rights' => $_SESSION['rights']
      ));
    }

    /**
     * @Route("/admin/cars/delete/{id_resa}", name="delete_resa_car_admin")
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
        $resa = $api->table_get("resa_car", array('id_resa' => $id_resa))[0];
        $car = $api->table_get("company_car", array('id_company_car' => $resa['id_company_car']))[0];
        foreach($api->table_get("work", array('id_user' => $_SESSION['id_user'])) as $work) {
          if($work['id_facility'] == $car['id_facility']) {
            $present = true;
            break;
          }
        }
      } else
        $present = true;

      if(!$present)
        return $this->redirectToRoute('accueil');

      $api->table_delete("resa_car", array('id_resa' => $id_resa));
      return $this->redirectToRoute('admin_cars');
    }
  }

 ?>
