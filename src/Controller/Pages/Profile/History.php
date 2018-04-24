<?php
  namespace App\Controller\Pages\Profile;

  use App\Controller\CustomApi;
  use App\Entity\State;
  use \DateTime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\RangeType;
  use Symfony\Component\Form\Extension\Core\Type\TimeType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\DateType;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
//Classe controlant l'historique des réservations d'un utilisateur
  class History extends Controller
  {
    /**
      * @Route("/profil/historique", name="history")
      */
    public function load_history(Request $request) {
      //On vérifie les autorisations
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      date_default_timezone_set('Europe/Paris');
      $resa_borne_futur = [];//Réservation d'une borne qui n'est pas encore passé
      $resa_borne_past = [];//Réservations d'une borne déjà passé
      $resa_car_futur = [];//Réservation d'un véhicule qui n'est pas encore passé
      $resa_car_past = [];//Réservations d'un véhicule déjà passé
      $api = new CustomApi();//l'interface pour l'API
      $state_of_form = new State();//Entité pour l'état des lieux

      $currentDate = new DateTime("now");
      $currentDate = date_format($currentDate, 'Y-m-d H:i:s');
      $options = ['AND end_date > \'' . $currentDate . '\' ', 'ORDER BY start_date ASC'];
      foreach($api->table_get("resa_borne", array('id_user' => $_SESSION['id_user']), $options) as $resa) {
        array_push($resa_borne_futur, array(
          'id_resa' => $resa['id_resa'],
          'start_date' => $resa['start_date'],
          'end_date' => $resa['end_date'],
          'charge' => $resa['charge'],
          'place' => $api->table_get("place", array('id_place' => $resa['id_place']))[0]['name'],
        ));
      }

      $options = ['AND end_date < \'' . $currentDate . '\' '];
      foreach($api->table_get("resa_borne", array('id_user' => $_SESSION['id_user']), $options) as $resa) {
        array_push($resa_borne_past, array(
          'id_resa' => $resa['id_resa'],
          'start_date' => $resa['start_date'],
          'end_date' => $resa['end_date'],
          'charge' => $resa['charge'],
          'place' => $api->table_get("place", array('id_place' => $resa['id_place']))[0]['name'],
        ));
      }

      $currentDate = new DateTime("now");
      $currentDate = date_format($currentDate, 'Y-m-d');
      $options = ['AND date_end > \'' . $currentDate . '\' '];
      foreach($api->table_get("resa_car", array('id_user' => $_SESSION['id_user']), $options) as $resa) {
        $car = $api->table_get("company_car", array('id_company_car' => $resa['id_company_car']))[0];
        $temp_resa = array(
          'id_resa' => $resa['id_resa'],
          'date_start' => substr($resa['date_start'], 0, 10),
          'date_end' => substr($resa['date_end'], 0, 10),
          'start_time' => substr($resa['start_time'], 0, 5),
          'end_time' => substr($resa['end_time'], 0, 5),
          'km_planned' => $resa['km_planned'],
          'id_reason' => $resa['id_reason'],
          'id_company_car' => $car['name'] . " (" . $car['model'] . ")",
          'facility' => $api->table_get("facility", array('id_facility' => ($car['id_facility'])))[0]['name']
        );

        if($resa['km_start'] == null) {
          $temp_resa['km_start_done'] = false;
          $km_start_form = $this->get("form.factory")->createNamedBuilder('km_start_form')
            ->add('km', NumberType::class, array('label' => "Kilométrage au départ: "))
            ->add('id_resa', HiddenType::class, array('data' => $resa['id_resa']))
            ->add('confirm', SubmitType::class, array('label' => 'Confirmer'))
        	  ->getForm();

          $temp_resa['km_start_form'] = $km_start_form->createView();
          if('POST' === $request->getMethod()) {
            $km_start_form->handleRequest($request);

            // On change la limite
            if($request->request->has('km_start_form') && $km_start_form->isValid()) {
              $api->table_update("resa_car", array('km_start' => $km_start_form->getData()['km']), array('id_resa' => $km_start_form->getData()['id_resa']));
              return $this->redirectToRoute('history');
            }
          }
        } else {
          $temp_resa['km_start_done'] = true;
          $temp_resa['km_start'] = $resa['km_start'];
        }

        if($resa['km_end'] == null) {
          $temp_resa['km_end_done'] = false;
          $km_start_form = $this->get("form.factory")->createNamedBuilder('km_end_form')
            ->add('km', NumberType::class, array('label' => "Kilométrage au départ: "))
            ->add('id_resa', HiddenType::class, array('data' => $resa['id_resa']))
            ->add('confirm', SubmitType::class, array('label' => 'Confirmer'))
        	  ->getForm();

          $temp_resa['km_end_form'] = $km_start_form->createView();
          if('POST' === $request->getMethod()) {
            $km_start_form->handleRequest($request);

            // On change la limite
            if($request->request->has('km_end_form') && $km_start_form->isValid()) {
              $api->table_update("resa_car", array('km_end' => $km_start_form->getData()['km']), array('id_resa' => $km_start_form->getData()['id_resa']));
              return $this->redirectToRoute('history');
            }
          }
        } else {
          $temp_resa['km_end_done'] = true;
          $temp_resa['km_end'] = $resa['km_end'];
        }


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
            return $this->redirectToRoute('history');
          }

          $temp_state['form'] = $state_form->createView();
        } else {
          $temp_state = array(
            'done' => true,
            'front' => ($state['front'] ? "Ok" : "Pas Ok"),
            'back' => ($state['back'] ? "Ok" : "Pas Ok"),
            'left_side' =>($state['left_side'] ? "Ok" : "Pas Ok"),
            'right_side' => ($state['right_side'] ? "Ok" : "Pas Ok"),
            'inside' => ($state['inside'] ? "Ok" : "Pas Ok"),
            'commentary' => $state['commentary'],
            'form' => NULL
          );
        }
        $temp_resa['state'] = $temp_state;
        array_push($resa_car_futur, $temp_resa);
      }

      $options = ['AND date_end < \'' . $currentDate . '\' '];
      foreach($api->table_get("resa_car", array('id_user' => $_SESSION['id_user']), $options) as $resa) {
        $car = $api->table_get("company_car", array('id_company_car' => $resa['id_company_car']))[0];
        $temp_resa = array(
          'id_resa' => $resa['id_resa'],
          'date_start' => substr($resa['date_start'], 0, 10),
          'date_end' => substr($resa['date_end'], 0, 10),
          'start_time' => substr($resa['start_time'], 0, 5),
          'end_time' => substr($resa['end_time'], 0, 5),
          'km_planned' => $resa['km_planned'],
          'id_reason' => $resa['id_reason'],
          'id_company_car' => $car['name'] . " (" . $car['model'] . ")",
          'facility' => $api->table_get("facility", array('id_facility' => ($car['id_facility'])))[0]['name']
        );

        $state = $api->table_get("state", array('id_state' => $resa['id_state']))[0];
        $temp_state = [];
        if($state['front'] == NULL) {
          $temp_state['done'] = false;
        } else {
          $temp_state = array(
            'done' => true,
            'front' => ($state['front'] ? "Ok" : "Pas Ok"),
            'back' => ($state['back'] ? "Ok" : "Pas Ok"),
            'left_side' =>($state['left_side'] ? "Ok" : "Pas Ok"),
            'right_side' => ($state['right_side'] ? "Ok" : "Pas Ok"),
            'inside' => ($state['inside'] ? "Ok" : "Pas Ok"),
            'commentary' => $state['commentary'],
            'form' => NULL
          );
        }
        $temp_resa['state'] = $temp_state;
        array_push($resa_car_past, $temp_resa);
      }

      return $this->render('profile/history.html.twig', array(
        'resa_borne_futur' => $resa_borne_futur,
        'resa_borne_past' => $resa_borne_past,
        'resa_car_futur' => $resa_car_futur,
        'resa_car_past' => $resa_car_past,
        'rights' => $_SESSION['rights']
      ));
    }

    /**
      * @Route("/profil/delete_resa_borne/{id_resa}", name="delete_resa_borne")
      */
    public function delete_resa_borne($id_resa) {
      //session_start();
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $resa = $api->table_get("resa_borne", array('id_resa' => $id_resa))[0];
      if($_SESSION['id_user'] != $resa['id_user'])
        return $this->redirectToRoute('accueil');

      $api->table_delete("resa_borne", array('id_resa' => $id_resa));
      $mail_body = array(
        'email' => $_SESSION['email'],
        'subject' => "Annulation de votre réservation n°" . $id_resa,
        'html' => "<p>Votre réservation de borne décrite ci-dessous a bien été supprimée :</p>
        <ul>
          <li>Début: " . $resa['start_date'] . "</li>
          <li>Fin: " . $resa['end_date'] . "</li>
          <li>Charge estimée en arrivant: " . $resa['charge'] . "</li>
          <li>Lieu: " . $api->table_get("place", array('id_place' => $resa['id_place']))[0]['name'] . "</li>
        <ul>
        "
      );

      $api->send_mail($mail_body);
      return $this->redirectToRoute('history');
    }

    /**
      * @Route("/profil/delete_resa_car/{id_resa}", name="delete_resa_car")
      */
    public function delete_resa_car($id_resa) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $resa = $api->table_get("resa_car", array('id_resa' => $id_resa))[0];
      if($_SESSION['id_user'] != $resa['id_user'])
        return $this->redirectToRoute('accueil');

      $api->table_update("state", array('id_resa' => NULL), array('id_state' => $resa['id_state']));
      $api->table_delete("resa_car", array('id_resa' => $id_resa));
      $api->table_delete("state", array('id_state' => $resa['id_state']));
      $mail_body = array(
        'email' => $_SESSION['email'],
        'subject' => "Annulation de votre réservation n°" . $id_resa,
        'html' => "<p>Votre réservation de voiture décrite ci-dessous a bien été supprimée :</p>
        <ul>
          <li>Début: " . substr($resa['date_start'], 0, 10) . " à " . $resa['start_time'] . "</li>
          <li>Fin: " . substr($resa['date_end'], 0, 10) . " à " . $resa['end_time'] . "</li>
          <li>Raison: " . $resa['id_reason'] . "</li>
          <li>Voiture: " . $api->table_get("company_car", array('id_company_car' => $resa['id_company_car']))[0]['name'] . "</li>
        <ul>
        "
      );

      $api->send_mail($mail_body);
      return $this->redirectToRoute('history');
    }
  }

 ?>
