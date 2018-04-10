<?php
  namespace App\Controller\Pages;

  use App\Controller\CustomApi;
  use App\Entity\PersonalCar;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $facilities = ["Yncréa", "ICL"];
        $places = ["Parking P1", "Parking P2"];
        $cars = [];
        $personal_car = new PersonalCar();

        foreach(($api->personal_car_get_all(array('id_user' => $_SESSION['id_user']))) as $car)
        {
            array_push($cars, array(
              'name' => $car['name'],
              'model' => $car['model'],
              'power' => $car['power']
            ));
        }

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

        return $this->render('profile/profile.html.twig', array(
              'first_name' => $_SESSION['first_name'],
              'last_name' => $_SESSION['last_name'],
              'id_status' => $_SESSION['id_status'],
              'email' => $_SESSION['email'],
              'phone_number' => $_SESSION['phone_number'],
              'facilities' => $facilities,
              'places' => $places,
              'personal_cars' => $cars,
              'personal_car_form' => $personal_car_form->createView()
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
  }

?>
