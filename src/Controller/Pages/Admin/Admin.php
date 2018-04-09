<?php
  namespace App\Controller\Pages\Admin;

  use App\Entity\Facility;
  use App\Entity\CompanyCar;
  use App\Entity\Place;
  use App\Entity\Borne;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class Admin extends Controller
  {
    /**
      * @Route("/admin")
      */
    public function load_admin(Request $request) {

      $facility = new Facility();
      $company_car = new CompanyCar();
      $place = new Place();
      $borne = new Borne();

      $facility_form = $this->createFormBuilder($facility)
      ->add('name', TextType::class)
      ->add('address', TextType::class)
      ->add('complementary', TextType::class)
      ->add('add_facility', SubmitType::class, array('label' => 'Ajouter l\'établissement'))
      ->getForm();
      $facility_form->handleRequest($request);

      $place_form = $this->createFormBuilder($place)
      ->add('name', TextType::class)
      ->add('address', TextType::class)
      ->add('id_facility', ChoiceType::class, array(
        'choices'  => array(
          'Yncréa' => 0,
          'IESEG' => 1,
          'ICL' => 2
        )))
      ->add('add_place', SubmitType::class, array('label' => 'Ajouter le lieu'))
      ->getForm();
      $place_form->handleRequest($request);

      $car_form = $this->createFormBuilder($company_car)
      ->add('name', TextType::class)
      ->add('model', TextType::class)
      ->add('power', NumberType::class)
      ->add('id_facility', ChoiceType::class, array(
        'choices'  => array(
          'Yncréa' => 0,
          'IESEG' => 1,
          'ICL' => 2
        )))
      ->add('add_company_car', SubmitType::class, array('label' => 'Ajouter la voiture'))
      ->getForm();
      $car_form->handleRequest($request);

      $borne_form = $this->createFormBuilder($borne)
      ->add('name', TextType::class)
      ->add('place', TextType::class)
      ->add('id_place', ChoiceType::class, array(
        'choices'  => array(
          'Parking P1' => 0,
          'Parking P2' => 1,
          'Parking IESEG' => 2
        )))
      ->add('add_borne', SubmitType::class, array('label' => 'Ajouter la borne'))
      ->getForm();
      $borne_form->handleRequest($request);

      if ($facility_form->isSubmitted() && $facility_form->isValid()) {
        $facility = $facility_form->getData();
      }

      if ($place_form->isSubmitted() && $place_form->isValid()) {
        $place = $place_form->getData();
      }

      if ($car_form->isSubmitted() && $car_form->isValid()) {
        $company_car = $car_form->getData();
      }

      if ($borne_form->isSubmitted() && $borne_form->isValid()) {
        $borne = $borne_form->getData();
      }

      return $this->render('admin/admin.html.twig', array(
            'facility_form' => $facility_form->createView(),
            'place_form' => $place_form->createView(),
            'car_form' => $car_form->createView(),
            'borne_form' => $borne_form->createView(),
      ));
    }
  }

 ?>
