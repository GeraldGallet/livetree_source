<?php
  namespace App\Controller\Pages;

  use App\Controller\CustomApi;
  use App\Entity\PersonalCar;
  use App\Entity\Work;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class Profile extends Controller
  {
    /**
      * @Route("/profil", name="profile")
      */
    public function load_profile(Request $request) {
      session_start();

      if(isset($_SESSION['id_user']))
      {
        $api = new CustomApi();
        $facilities = [];
        $facilities_choices = [];
        $places = ["Parking P1", "Parking P2"];
        $cars = [];
        $personal_car = new PersonalCar();
        $work = new Work();

        foreach(($api->personal_car_get_all(array('id_user' => $_SESSION['id_user']))) as $car)
        {
            array_push($cars, array(
              'name' => $car['name'],
              'model' => $car['model'],
              'power' => $car['power']
            ));
        }

        foreach(($api->facility_get_all()) as $fac)
        {
            $facilities_choices[$fac['name']] = $fac['id_facility'];
        }

        foreach(($api->work_get($_SESSION{'id_user'})) as $temp_work)
        {
          $temp_fac = array_search($temp_work['id_facility'], $facilities_choices);
          array_push($facilities, array(
            'id_facility' => $temp_work['id_facility'],
            'name' => $temp_fac
          ));
          unset($facilities_choices[$temp_fac]);
        }


        $work_form = $this->createFormBuilder($work)
            ->add('id_facility', ChoiceType::class, array(
              'choices'  => $facilities_choices))
            ->add('add_work', SubmitType::class, array('label' => 'J\'ai accès à cet établissement'))
            ->getForm();
        $work_form->handleRequest($request);

        $personal_car_form = $this->createFormBuilder($personal_car)
            ->add('name', TextType::class)
            ->add('model', TextType::class)
            ->add('power', NumberType::class)
            ->add('add_personal_car', SubmitType::class, array('label' => 'Ajouter une voiture'))
            ->getForm();
        $personal_car_form->handleRequest($request);


        if ($personal_car_form->isSubmitted() && $personal_car_form->isValid()) {
          $personal_car = $personal_car_form->getData();

          $api->personal_car_add(array(
            'name' => $personal_car->getName(),
            'model' => $personal_car->getModel(),
            'power' => $personal_car->getPower(),
            'id_user' => $_SESSION['id_user'],
          ));

          return $this->redirectToRoute('profile');
        }

        if ($work_form->isSubmitted() && $work_form->isValid()) {
          $work = $work_form->getData();

          $api->work_add($_SESSION['id_user'], $work->getIdFacility());

          return $this->redirectToRoute('profile');
        }

        return $this->render('profile/profile.html.twig', array(
              'first_name' => $_SESSION['first_name'],
              'last_name' => $_SESSION['last_name'],
              'id_status' => $_SESSION['id_status'],
              'email' => $_SESSION['email'],
              'phone_number' => $_SESSION['phone_number'],
              'facilities' => $facilities,
              'places' => $places,
              'personal_cars' => $cars,
              'personal_car_form' => $personal_car_form->createView(),
              'work_form' => $work_form->createView()
        ));
      } else
      {
        return $this->redirectToRoute('accueil');
      }
    }

    /**
     * @Route("/profil/personal_car/delete/{car_name}", name="delete_personal_car")
     */
    public function delete_personal_car($car_name) {
      session_start();
      $api = new CustomApi();
      $api->personal_car_delete($_SESSION['id_user'], $car_name);
      return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/profil/work/delete/{id_facility}", name="delete_work")
     */
    public function delete_work($id_facility) {
      session_start();
      $api = new CustomApi();
      $api->work_delete($_SESSION['id_user'], $id_facility);
      return $this->redirectToRoute('profile');
    }
  }

?>
