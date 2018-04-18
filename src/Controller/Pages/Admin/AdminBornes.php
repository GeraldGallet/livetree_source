<?php
  namespace App\Controller\Pages\Admin;

  use App\Entity\ReservationBorne;
  use App\Entity\FiltreReservationBorne;
  use App\Controller\CustomApi;

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


  class AdminBornes extends Controller
  {
    /**
      * @Route("/admin/bornes", name="admin_bornes")
      */
    public function load_admin_bornes(Request $request) {
      if(!isset($_SESSION))
        session_start();

      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $place_choices = [];
      $resas = [];

      if($rights == 3) {
        foreach($api->table_get_all("place") as $place) {
          $place_choices[$place['name']] = $place['id_place'];
        }
      } else {
        foreach($api->table_get("has_access", array('id_user' => $_SESSION['id_user'])) as $acc) {
          $place_choices[$api->table_get("place", array('id_place' => $acc['id_place']))[0]['name']] = $acc['id_place'];
        }
      }

      $resa_borne = new ReservationBorne();
      $resa_form = $this->createFormBuilder($resa_borne)
      ->add('start_date', DateTimeType::class, array('widget' => 'single_text'))
      ->add('end_date', DateTimeType::class, array('widget' => 'single_text'))
      ->add('charge', NumberType::class)
      ->add('id_user', NumberType::class)
      ->add('id_place', ChoiceType::class, array(
        'choices'  => $place_choices))
      ->add('add_resa', SubmitType::class, array('label' => 'Créer la réservation'))
      ->getForm();
      $resa_form->handleRequest($request);

      $filter = new FiltreReservationBorne();
      $filter_form = $this->createFormBuilder($filter)
      ->add('date_time', DateType::class,array('widget' => 'single_text'))
      ->add('start_time', TimeType::class, array('widget' => 'single_text'))
      ->add('end_time', TimeType::class, array('widget' => 'single_text'))
      ->add('charge', NumberType::class)
      ->add('id_user', NumberType::class)
      ->add('id_place', ChoiceType::class, array(
        'choices'  => $place_choices))
      ->add('filter_resa', SubmitType::class, array('label' => 'Filtrer'))
      ->getForm();
      $filter_form->handleRequest($request);

      if($filter_form->isSubmitted() && $filter_form->isValid()) {
        $filter = $filter_form->getData();
        if($filter->getDateTime() == NULL)
          $date = NULL;
        else
          $date = date_format($filter->getDateTime(), 'Y-m-d-H-i');

        if($filter->getStartTime() == NULL)
          $start = NULL;
        else
          $start = $filter->getStartTime()->format('H:i');

        if($filter->getEndTime() == NULL)
          $end = NULL;
        else
          $end = $filter->getEndtime()->format('H:i');

        $filter_options = array(
          'id_place' => $filter->getIdPlace(),
          'date_time' => $date,
          'start_time' => $start,
          'end_time' => $end,
          'charge' => $filter->getCharge(),
          'id_user' => $filter->getIdUser(),
        );

        foreach($filter_options as $key => $value) {
          if($value == NULL)
            unset($filter_options[$key]);
        }
        $resas = $api->table_get("resa_borne", $filter_options);
        return $this->render('admin/admin_bornes.html.twig', array(
              'resa_bornes' => $resas,
              'resa_form' => $resa_form->createView(),
              'filter_form' => $filter_form->createView()
        ));

      } else {
        foreach($place_choices as $id_place) {
          foreach($api->table_get("resa_borne", array('id_place' => $id_place)) as $resa) {
            array_push($resas, array(
              'id_resa' => $resa['id_resa'],
              'date_time' => substr($resa['date_creation'], 0, 10),
              'start_time' => $resa['start_date'],
              'end_time' => $resa['end_date'],
              'charge' => $resa['charge'],
              'id_place' => array_search($id_place, $place_choices),
              'id_user' => $api->table_get("user", array('id_user' => $resa['id_user']))[0]['email']
            ));
          }
        }
      }

      if($resa_form->isSubmitted() && $resa_form->isValid()) {
          $resa_borne = $resa_form->getData();

          $api->table_add("resa_borne", array(
            'date_creation' => date_format($resa_borne->getDateCreation(), 'Y-m-d'),
            'start_date' => $resa_borne->getStartDate()->format( 'Y-m-d H:i:s'),
            'end_date' => $resa_borne->getEndDate()->format( 'Y-m-d H:i:s'),
            'charge' => $resa_borne->getCharge(),
            'id_user' => $resa_borne->getIdUser(),
            'id_place' => $resa_borne->getIdPlace()
          ));

          return $this->redirectToRoute('admin_bornes');
      }

      return $this->render('admin/admin_bornes.html.twig', array(
            'resa_bornes' => $resas,
            'resa_form' => $resa_form->createView(),
            'filter_form' => $filter_form->createView()
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

      $api->table_delete("resa_borne", array('id_resa' => $id_resa));
      return $this->redirectToRoute('admin_bornes');
    }
  }

 ?>
