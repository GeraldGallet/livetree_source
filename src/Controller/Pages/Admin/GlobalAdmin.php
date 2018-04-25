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

  class GlobalAdmin extends Controller
  {
    /**
      * @Route("/admin", name="admin")
      */
    public function load_admin(Request $request) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 2) {
        return $this->redirectToRoute('accueil');
      }

      $facility = new Facility();
      $company_car = new CompanyCar();
      $place = new Place();
      $borne = new Borne();
      $api = new CustomApi();

      $facility_form_view = NULL;
      $facilities = [];
      $choices_facilities = [];
      $choices_places = [];
      $places = [];
      $cars = [];
      $bornes = [];

      if($rights == 3) {
        foreach($api->table_get_all("facility") as $temp_facility)
        {
          array_push($facilities, array(
            'name' => $temp_facility['name'],
            'address' => $temp_facility['address'],
            'complementary' => $temp_facility['complementary']
          ));
          $choices_facilities[$temp_facility['name']] = $temp_facility['id_facility'];

        }
      } else {
        foreach($api->table_get("work", array('id_user' => $_SESSION['id_user'])) as $temp_work)
        {
          $temp_facility = $api->table_get("facility", array('id_facility' => $temp_work['id_facility']))[0];
          array_push($facilities, array(
            'name' => $temp_facility['name'],
            'address' => $temp_facility['address'],
            'complementary' => $temp_facility['complementary']
          ));
          $choices_facilities[$temp_facility['name']] = $temp_facility['id_facility'];
        }
      }

      foreach($choices_facilities as $id_fac)
      {
        foreach($api->table_get("place", array('id_facility' => $id_fac)) as $temp_place) {
          array_push($places, array(
            'name' => $temp_place['name'],
            'address' => $temp_place['address'],
            'facility' => $api->table_get("facility", array('id_facility' => $temp_place['id_facility']))[0]['name'],
            'id_place' => $temp_place['id_place']
          ));
          $choices_places[$temp_place['name']] = $temp_place['id_place'];
        }
      }

      foreach($choices_facilities as $id_fac) {
        foreach($api->table_get("company_car", array("id_facility" => $id_fac)) as $temp_car)
        {
            array_push($cars, array(
              'name' => $temp_car['name'],
              'model' => $temp_car['model'],
              'facility' => array_search($temp_car['id_facility'], $choices_facilities),
              'power' => $temp_car['power'],
              'id_company_car' => $temp_car['id_company_car']
            ));
        }
      }

      foreach($choices_places as $id_place) {
        foreach($api->table_get("borne", array('id_place' => $id_place)) as $temp_borne)
        {
            array_push($bornes, array(
              'name' => $temp_borne['name'],
              'place' => $temp_borne['place'],
              'id_place' => $temp_borne['id_place'],
              'id_borne' => $temp_borne['id_borne'],
              'desc_place' => array_search($temp_borne['id_place'], $choices_places)
            ));
        }
      }

      $facility_form = $this->createFormBuilder($facility)
      ->add('name', TextType::class, array('label' => "Nom: "))
      ->add('address', TextType::class, array('label' => "Adresse: "))
      ->add('complementary', TextType::class, array('label' => "Informations complémentaires: "))
      ->add('add_facility', SubmitType::class, array(
        'label' => 'Ajouter l\'établissement',
        'attr' => [
          'class' => "btn btn-outline-secondary",
          ]))
      ->getForm();
      $facility_form->handleRequest($request);
      $facility_form_view = $facility_form->createView();

      if ($facility_form->isSubmitted() && $facility_form->isValid()) {
        $facility = $facility_form->getData();
        $api->table_add("facility", array(
          'name' => $facility->getName(),
          'address' => $facility->getAddress(),
          'complementary' => $facility->getComplementary()
        ));
        return $this->redirectToRoute('admin');
      }


      $place_form = $this->createFormBuilder($place)
      ->add('name', TextType::class, array('label' => "Nom: "))
      ->add('address', TextType::class, array('label' => "Adresse: "))
      ->add('id_facility', ChoiceType::class, array(
        'choices'  => $choices_facilities,
        'label' => "Etablissement concerné: "
      ))
      ->add('add_place', SubmitType::class, array(
        'label' => 'Ajouter le lieu',
        'attr' => [
          'class' => "btn btn-outline-secondary",
          ]))
      ->getForm();
      $place_form->handleRequest($request);

      $car_form = $this->createFormBuilder($company_car)
      ->add('name', TextType::class, array('label' => "Nom: "))
      ->add('model', TextType::class, array('label' => "Modèle: "))
      ->add('power', NumberType::class, array('label' => "Puissance (kW): "))
      ->add('id_facility', ChoiceType::class, array(
        'choices'  => $choices_facilities,
        'label' => "Etablissement concerné: "
      ))
      ->add('add_company_car', SubmitType::class, array(
        'label' => 'Ajouter la voiture',
        'attr' => [
          'class' => "btn btn-outline-secondary",
          ]))
      ->getForm();
      $car_form->handleRequest($request);

      $borne_form = $this->createFormBuilder($borne)
      ->add('name', TextType::class, array('label' => "Nom: "))
      ->add('place', TextType::class, array('label' => "Emplacement (ex. 2e etage): "))
      ->add('id_place', ChoiceType::class, array(
        'choices'  => $choices_places,
        'label' => "Lieu: "
      ))
      ->add('add_borne', SubmitType::class, array(
        'label' => 'Ajouter la borne',
        'attr' => [
          'class' => "btn btn-outline-secondary",
          ]))
      ->getForm();
      $borne_form->handleRequest($request);


      if ($place_form->isSubmitted() && $place_form->isValid()) {
        $place = $place_form->getData();
        $api->table_add("place", array(
          'name' => $place->getName(),
          'address' => $place->getAddress(),
          'id_facility' => $place->getIdFacility(),
        ));
        return $this->redirectToRoute('admin');
      }

      if ($car_form->isSubmitted() && $car_form->isValid()) {
        $company_car = $car_form->getData();
        $api->table_add("company_car", array(
          'name' => $company_car->getName(),
          'model' => $company_car->getModel(),
          'power' => $company_car->getPower(),
          'id_facility' => $company_car->getIdFacility(),
        ));
        return $this->redirectToRoute('admin');
      }

      if ($borne_form->isSubmitted() && $borne_form->isValid()) {
        $borne = $borne_form->getData();
        $api->table_add("borne", array(
          'name' => $borne->getName(),
          'place' => $borne->getPlace(),
          'id_place' => $borne->getIdPlace(),
        ));
        return $this->redirectToRoute('admin');
      }

      return $this->render('admin/admin.html.twig', array(
            'facilities' => $facilities,
            'places' => $places,
            'cars' => $cars,
            'bornes' => $bornes,
            'facility_form' => $facility_form_view,
            'place_form' => $place_form->createView(),
            'car_form' => $car_form->createView(),
            'borne_form' => $borne_form->createView(),
            'rights' => $rights
      ));
    }

    /**
     * @Route("/admin/facility/delete/{name}", name="delete_facility")
     */
     public function delete_facility($name) {
      $api = new CustomApi();
      $api->table_delete("facility", array('name' => $name));
      return $this->redirectToRoute('admin');
     }

   /**
    * @Route("/admin/place/delete/{id_place}", name="delete_place")
    */
    public function delete_place($id_place) {
     $api = new CustomApi();
     $api->table_delete("place", array('id_place' => $id_place));
     return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/company_car/delete/{id_company_car}", name="delete_company_car")
     */
     public function delete_company_car($id_company_car) {
      $api = new CustomApi();
      $api->table_delete("company_car", array('id_company_car' => $id_company_car));
      return $this->redirectToRoute('admin');
     }

     /**
      * @Route("/admin/borne/delete/{id_borne}", name="delete_borne")
      */
      public function delete_borne($id_borne) {
       $api = new CustomApi();
       $api->table_delete("borne", array('id_borne' => $id_borne));
       return $this->redirectToRoute('admin');
      }
  }

 ?>
