<?php
  namespace App\Controller\Pages\Admin;

  use App\Entity\Facility;
  use App\Entity\CompanyCar;
  use App\Entity\Place;
  use App\Entity\Borne;
  use App\Controller\CustomApi;

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
      * @Route("/admin", name="admin")
      */
    public function load_admin(Request $request) {

      $facility = new Facility();
      $company_car = new CompanyCar();
      $place = new Place();
      $borne = new Borne();
      $api = new CustomApi();

      $facilities = [];
      $choices_facilities = [];
      foreach($api->facility_get_all() as $temp_facility)
      {
          array_push($facilities, array(
            'name' => $temp_facility['name'],
            'address' => $temp_facility['address'],
            'complementary' => $temp_facility['complementary']
          ));
          $choices_facilities[$temp_facility['name']] = $temp_facility['id_facility'];
      }

      $places = [];
      foreach($api->place_get_all() as $temp_place)
      {
          array_push($places, array(
            'name' => $temp_place['name'],
            'address' => $temp_place['address'],
            'id_facility' => $temp_place['id_facility'],
            'id_place' => $temp_place['id_place']
          ));
      }

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
        'choices'  => $choices_facilities))
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
        $api->facility_add(array(
          'name' => $facility->getName(),
          'address' => $facility->getAddress(),
          'complementary' => $facility->getComplementary()
        ));
        return $this->redirectToRoute('admin');
      }

      if ($place_form->isSubmitted() && $place_form->isValid()) {
        $place = $place_form->getData();
        $api->place_add(array(
          'name' => $place->getName(),
          'address' => $place->getAddress(),
          'id_facility' => $place->getIdFacility()
        ));
        return $this->redirectToRoute('admin');
      }

      if ($car_form->isSubmitted() && $car_form->isValid()) {
        $company_car = $car_form->getData();
      }

      if ($borne_form->isSubmitted() && $borne_form->isValid()) {
        $borne = $borne_form->getData();
      }

      return $this->render('admin/admin.html.twig', array(
            'facilities' => $facilities,
            'places' => $places,
            'facility_form' => $facility_form->createView(),
            'place_form' => $place_form->createView(),
            'car_form' => $car_form->createView(),
            'borne_form' => $borne_form->createView(),
      ));
    }

    /**
     * @Route("/admin/facility/delete/{name}", name="delete_facility")
     */
     public function delete_facility($name) {
      session_start();
      $api = new CustomApi();
      $api->facility_delete($name);
      return $this->redirectToRoute('admin');
     }

   /**
    * @Route("/admin/place/delete/{id_place}", name="delete_place")
    */
    public function delete_place($id_place) {
     session_start();
     $api = new CustomApi();
     $api->place_delete($id_place);
     return $this->redirectToRoute('admin');
    }
  }

 ?>
