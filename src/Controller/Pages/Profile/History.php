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

  class History extends Controller
  {
    /**
      * @Route("/profil/historique", name="history")
      */
    public function load_history(Request $request) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      date_default_timezone_set('Europe/Paris');
      $resa_borne_futur = [];
      $resa_borne_past = [];
      $resa_car_futur = [];
      $resa_car_past = [];
      $api = new CustomApi();
      $state_of_form = new State();

      $currentDate = new DateTime("now");
      $currentDate = date_format($currentDate, 'Y-m-d H:i:s');
      $options = ['AND end_date > \'' . $currentDate . '\' '];
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
            'front' => $state['front'],
            'back' => $state['back'],
            'left_side' => $state['left_side'],
            'right_side' => $state['right_side'],
            'inside' => $state['inside'],
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
            'front' => $state['front'],
            'back' => $state['back'],
            'left_side' => $state['left_side'],
            'right_side' => $state['right_side'],
            'inside' => $state['inside'],
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
          <li>Début: " . $resa['date_start'] . " à " . $resa['start_time'] . "</li>
          <li>Fin: " . $resa['date_end'] . " à " . $resa['end_time'] . "</li>
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