<?php
  namespace App\Controller\Pages\Profile;

  use App\Controller\CustomApi;
  use App\Entity\PersonalCar;
  use App\Entity\Work;
  use App\Entity\Access;
  use \DateTime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//Classe controlant le profil d'un utilisateur
  class Profile extends Controller
  {
    /**
      * @Route("/profil", name="profile")
      */
    public function load_profile(Request $request) {
      //On vérifie si la personne est connecté
      if(isset($_SESSION['id_user']))
      {
        $api = new CustomApi();//L'interface pour l'API
        $facilities = [];
        $facilities_choices = [];//Choix des établissement de l'utilisateur
        $places = [];
        $places_choices = [];//Choix des diffèrents lieux accessible par l'utilisateur
        $cars = [];//Voiture de l'utilisateur
        $personal_car = new PersonalCar();//entité pour crée un nouveau véhicule personnel
        $work = new Work();//entité pour crée un nouveau lieu
        $access = new Access();//entité pour dire si l'utilistauer a accès au lieu

        //On va chercher dans la BDD les voitures personnels de l'utilisteurs
        foreach($api->table_get("personal_car", array('id_user' => $_SESSION['id_user'])) as $car)
        {
            array_push($cars, array(
              'name' => $car['name'],
              'model' => $car['model'],
              'power' => $car['power']
            ));
        }
        //On va chercher dans la BDD les établissements accessible par l'utilisateur
        foreach(($api->table_get_all("facility")) as $fac)
        {
            $facilities_choices[$fac['name']] = $fac['id_facility'];
        }
        //...............................................
        foreach($api->table_get("work", array('id_user' => $_SESSION{'id_user'})) as $temp_work)
        {
          $temp_fac = array_search($temp_work['id_facility'], $facilities_choices);
          array_push($facilities, array(
            'name' => $temp_fac
          ));
          unset($facilities_choices[$temp_fac]);
        //On va chercher dans la BDD les établissemnts et les lieux accessible par l'utilisateur
          foreach($api->table_get("place", array('id_facility' => $temp_work['id_facility'])) as $temp_place) {
            $facilities_choices[$fac['name']] = $fac['id_facility'];
            $places_choices[$temp_place['name']] = $temp_place['id_place'];
          }
        }

        foreach($api->table_get("has_access", array('id_user' => $_SESSION['id_user'])) as $acc) {
          $name_place = array_search($acc['id_place'], $places_choices);
          unset($places_choices[$name_place]);
          array_push($places, array(
            'name' => $name_place,
            'id_place' => $acc['id_place']
          ));
        }

        if(sizeof($places_choices) > 0) {//On permet à l'utilisateur de renseigner les lieux qui lui sont disponibles en fonction de son établissement d'origine, ceci dans un petit formulaire
          $access_form = $this->createFormBuilder($access)
              ->add('id_place', ChoiceType::class, array(
                'choices'  => $places_choices,
                'label' => 'Nouveau lieu : '))
              ->add('add_access', SubmitType::class, array(
                'label' => 'J\'ai accès à ce lieu',
                'attr' => [
                  'class' => "btn btn-outline-secondary",
                  ]))
              ->getForm();
          $access_form->handleRequest($request);
          $access_form_view = $access_form->createView();
          //Si le formulaire et envoyé et validé
          if ($access_form->isSubmitted() && $access_form->isValid()) {
            $access = $access_form->getData();//On récupère les données inscritent

            $api->table_add("has_access", array(//On ajoute à la BDD le nouveau lieu accessible par l'utilisateur
              'id_user' => $_SESSION['id_user'],
              'id_place' => $access->getIdPlace()
            ));
            return $this->redirectToRoute('profile');//On le renvoie vers le profile
          }
        } else {
          $access_form_view = null;
        }
        //On permet à l'utilisateur d'ajouter une nouvelle voiture et cela dans un petit formulaire
        $personal_car_form = $this->createFormBuilder($personal_car)
            ->add('name', TextType::class, array('label' => "Nom "))
            ->add('model', TextType::class, array('label' => "Modèle "))
            ->add('power', NumberType::class, array('label' => "Puissance (kW) "))
            ->add('add_personal_car', SubmitType::class, array(
              'label' => 'Ajouter une voiture',
              'attr' => [
                'class' => "btn btn-outline-secondary",
                ]))
            ->getForm();
        $personal_car_form->handleRequest($request);

        //Si le formulaire est envoyé et validé
        if ($personal_car_form->isSubmitted() && $personal_car_form->isValid()) {
          $personal_car = $personal_car_form->getData();//On récupère les données renseignées

          $api->table_add("personal_car", array(//On ajoute à notre BDD la nouvelle voiture de l'utilisateur
            'name' => $personal_car->getName(),
            'model' => $personal_car->getModel(),
            'power' => $personal_car->getPower(),
            'id_user' => $_SESSION['id_user'],
          ));

          return $this->redirectToRoute('profile');//Si le formulaire est faux on le redirige vers la page profil
        }

        return $this->render('profile/profile.html.twig', array(//On affiche le profil de l'utilisateur avec ses informations
              'first_name' => $_SESSION['first_name'],
              'last_name' => $_SESSION['last_name'],
              'id_status' => $_SESSION['id_status'],
              'email' => $_SESSION['email'],
              'phone_number' => $_SESSION['indicative'] . " " . $_SESSION['phone_number'],
              'facilities' => $facilities,
              'places' => $places,
              'personal_cars' => $cars,
              'personal_car_form' => $personal_car_form->createView(),
              'access_form' => $access_form_view,
              'rights' => $_SESSION['rights']
        ));
      } else
        return $this->redirectToRoute('accueil');//Si la session n'est pas active on le renvoie vers accueil
    }

    /**
     * @Route("/profil/personal_car/delete/{car_name}", name="delete_personal_car")
     */
     //Fonction permettant de supprimer un véhicule personnel par son utilisateur
    public function delete_personal_car($car_name) {
      $api = new CustomApi();
      $api->table_delete("personal_car", array(
        'id_user' => $_SESSION['id_user'],
        'name' => $car_name
      ));
      return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/profil/work/delete/{id_facility}", name="delete_work")
     */
     //Fonction permettant de supprimer son établissement par son utilisateur
    public function delete_work($id_facility) {
      $api = new CustomApi();
      $api->table_delete("work", array(
        'id_user' => $_SESSION['id_user'],
        'id_facility' => $id_facility
      ));
      return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/profil/access/delete/{id_place}", name="delete_access")
     */
     //Fonction permettant de supprimer son accès à un lieu par son utilisateur
    public function delete_access($id_place) {
      $api = new CustomApi();
      $api->table_delete("has_access", array(
        'id_user' => $_SESSION['id_user'],
        'id_place' => $id_place
      ));
      return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/suppression", name="delete_account")
     */
     //Fonction permettant à l'utilisateur de supprimer son compte
    public function delete_account() {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');
      return $this->render('profile/delete.html.twig', array(
        'rights' => $_SESSION['rights'],
        'email' => $_SESSION['email']
      ));
    }

    /**
     * @Route("/suppression_valider", name="delete_account_sure")
     */
    public function delete_account_sure() {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $id_user = $_SESSION['id_user'];
      $api = new CustomApi();
      $currentDate = new DateTime("now");
      $currentDate = date_format($currentDate, 'Y-m-d H:i:s');
      $options = ['AND start_date > \'' . $currentDate . '\' '];
      $api->table_delete("resa_borne", array('id_user' => $id_user), $options);
      $api->table_update("resa_borne", array('id_user' => null, 'id_personal_car' => null), array('id_user' => $id_user));

      $currentDate = new DateTime("now");
      $currentDate = date_format($currentDate, 'Y-m-d');
      $options = ['AND date_end > \'' . $currentDate . '\' '];
      foreach($api->table_get("resa_car", array('id_user' => $id_user), $options) as $resa) {
        $api->table_update("state", array('id_resa' => NULL), array('id_state' => $resa['id_state']));
        $api->table_delete("resa_car", array('id_resa' => $id_resa));
        $api->table_delete("state", array('id_state' => $resa['id_state']));
      }


      $api->table_update("resa_car", array('id_user' => null), array('id_user' => $id_user));
      $api->table_delete("personal_car", array('id_user' => $id_user));
      $api->table_delete("has_access", array('id_user' => $id_user));
      $api->table_delete("work", array('id_user' => $id_user));
      $api->table_delete("user", array('id_user' => $id_user));

      return $this->redirectToRoute('deconnect');
    }
  }

?>
