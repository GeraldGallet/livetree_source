<?php
  namespace App\Controller\Pages\Admin;

  use App\Entity\ReservationBorne;
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


  class AdminBornes extends Controller
  {
    /**
      * @Route("/admin/bornes", name="admin_bornes")
      */
    public function load_admin_bornes(Request $request) {
      session_start();
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = 2;
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $place_choices = [];
      $resas = [];

      if($rights == 3) {
        foreach($api->place_get_all() as $place) {
          $place_choices[$place['name']] = $place['id_place'];
        }
      } else {
        foreach($api->has_access_get($_SESSION['id_user']) as $acc) {
          $place_choices[$api->place_get_by_id($acc['id_place'])['name']] = $acc['id_place'];
        }
      }

      foreach($place_choices as $id_place) {
        foreach($api->reservation_borne_get_by_place($id_place) as $resa) {
          array_push($resas, array(
            'id_resa' => $resa['id_resa'],
            'date_time' => substr($resa['date_resa'], 0, 10),
            'start_time' => $resa['start_time'],
            'end_time' => $resa['end_time'],
            'charge' => $resa['charge'],
            'id_place' => array_search($id_place, $place_choices),
            'id_user' => $api->user_get_by_id($resa['id_user'])['email']
          ));
        }
      }

      $resa_borne = new ReservationBorne();
      $resa_form = $this->createFormBuilder($resa_borne)
      ->add('date_time', DateType::class)
      ->add('start_time', TimeType::class)
      ->add('end_time', TimeType::class)
      ->add('charge', NumberType::class)
      ->add('id_user', NumberType::class)
      ->add('id_place', ChoiceType::class, array(
        'choices'  => $place_choices))
      ->add('add_place', SubmitType::class, array('label' => 'Créer la réservation'))
      ->getForm();
      $resa_form->handleRequest($request);

      return $this->render('admin/admin_bornes.html.twig', array(
            'resa_bornes' => $resas,
            'resa_form' => $resa_form->createView()
      ));
    }

    /**
     * @Route("/admin/bornes/delete/{id_resa}", name="delete_resa_borne")
     */
    public function delete_resa($id_resa) {
      // Delete a reservation
      session_start();
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = 2;
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $present = false;
      $api = new CustomApi();
      if($rights == 2) {
        $resa = $api->reservation_borne_get_by_id($id_resa);
        foreach($api->has_access_get($_SESSION['id_user']) as $acc) {
          if($acc['id_place'] == $resa['id_place']) {
            $present = true;
            break;
          }
        }
      }

      if(!$present)
        return $this->redirectToRoute('accueil');
        
      $api->reservation_borne_delete($id_resa);
      return $this->redirectToRoute('admin_bornes');
    }
  }

 ?>
