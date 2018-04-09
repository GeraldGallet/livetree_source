<?php
  namespace App\Controller\Pages\Admin;

  use App\Entity\ReservationBorne;
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
      * @Route("/admin/bornes")
      */
    public function load_admin_bornes(Request $request) {
      $resa = array(
        'id_resa' => -1,
        'date_time' => 0,
        'start_time' => 1,
        'end_time' => 2,
        'charge' => 3,
        'id_place' => 4,
        'id_user' => 5
      );
      $resas = [$resa, $resa, $resa, $resa];

      $resa_borne = new ReservationBorne();
      $resa_form = $this->createFormBuilder($resa_borne)
      ->add('date_time', DateType::class)
      ->add('start_time', TimeType::class)
      ->add('end_time', TimeType::class)
      ->add('charge', NumberType::class)
      ->add('id_user', NumberType::class)
      ->add('id_place', ChoiceType::class, array(
        'choices'  => array(
          'Parking P1' => 0,
          'Parking P2' => 1,
          'Parking IESEG' => 2
        )))
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
    }
  }

 ?>
