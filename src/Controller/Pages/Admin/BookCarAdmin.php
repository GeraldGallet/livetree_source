<?php
  namespace App\Controller\Pages\Admin;

  use App\Controller\CustomApi;
  use App\Entity\AdminReservationCar;
  use App\Entity\State;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Form\Extension\Core\Type\RangeType;
  use Symfony\Component\Form\Extension\Core\Type\TimeType;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
  use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
  use Symfony\Component\Form\Extension\Core\Type\DateType;
  use Symfony\Component\Form\Extension\Core\Type\HiddenType;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Routing\Annotation\Route;

  // Classes controlant la page Admin des réservations de voitures
  class BookCarAdmin extends Controller
  {
    /**
      * @Route("/admin/voitures", name="admin_cars")
      */
    public function load_admin_cars(Request $request) {
      // On vérifie les droits
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $resa_car = []; // Les réservations affichées sur la page
      $resa_car_extract = []; // Même chose qu'au-dessus mais dans un format possible a extraire en JSON
      $reason_choices = []; // Les différents choix de raisons pour le formulaire d'ajout
      $company_car_choices = []; // Les différentes voitures de fonctions pour l'ajout
      $facility_choices = []; // Tous les établissements auxquels on a accès
      $user_choice = []; // Tous les utilisateurs de nos etablissements

      $api = new CustomApi(); // L'interface pour l'API
      $state_of_form = new State(); // L'entité d'état des lieux

      // Différenciation Admin / Super-Admin pour le chargement des etablissements
      if($rights == 2) {
        foreach($api->table_get("work", array('id_user' => $_SESSION['id_user'])) as $work)
          array_push($facility_choices, $work['id_facility']);
      } else {
        foreach($api->table_get_all("facility") as $fac)
          array_push($facility_choices, $fac['id_facility']);
      }

      // Chargement des raisons
      foreach($api->table_get_all("reason") as $reason)
        $reason_choices[$reason['id_reason']] = $reason['id_reason'];

      // On charge les utilisateurs des etablissements charges au-dessus
      foreach($facility_choices as $id) {
        foreach($api->table_get("work", array('id_facility' => $id)) as $work) {
          $user = $api->table_get("user", array('id_user' => $work['id_user']))[0];
          $user_choice[$user['email']] = $user['id_user'];
        }
      }

      // Chargement des voitures de fonctions des etablissements
      foreach($api->table_get("work", array('id_user' => $_SESSION['id_user'])) as $work) {
        foreach($api->table_get("company_car", array('id_facility' => $work['id_facility'])) as $temp_car) {
          $company_car_choices[$temp_car['name']] = $temp_car['id_company_car'];
        }
      }

      // Les options pour trouver les réservations de voitures
      // On ne prendra que celles concernant les véhicules chargés
      $cco = "AND (";
      foreach($facility_choices as $id_fac) {
        foreach($api->table_get("company_car", array('id_facility' => $id_fac)) as $cc) {
          $cco .= 'id_company_car = ' . $cc['id_company_car'] . " OR ";
        }
      }
      $cco .= "1)";

      // Pour l'affichage sur le template
      $_SESSION['total_cars'] = sizeof($api->table_get("resa_car", array(), [$cco]));

      // On veut les résas triées par date et on en veut <limit> par page
      $options = [$cco, "ORDER BY date_start DESC ", "LIMIT " . $_SESSION['limit_cars'] . " ", "OFFSET " . $_SESSION['offset_cars'] . " "];
      $resa_car_db = $api->table_get("resa_car", array(), $options); // Chargement des réservations

      if(sizeof($resa_car_db != 0)) {
        $actual_max = $_SESSION['offset_cars'] + sizeof($resa_car_db); // Pour le template Twig

        foreach($resa_car_db as $resa) // On prépare chaque resa a etre affichee
        {
          $car = $api->table_get("company_car", array('id_company_car' => $resa['id_company_car']))[0];

          if($resa['id_user'] == null)
            $id_user = "Ce compte a été supprimé";
          else {
            $res = $api->table_get("user", array('id_user' => $resa['id_user']))[0];
            $id_user = $res['first_name'] . " " . $res['last_name'] . " (" . $res['id_user'] . ")";
          }

          // On charge les informations directes
          $temp_resa = array(
            'id_resa' => $resa['id_resa'],
            'date_start' => substr($resa['date_start'], 0, 10),
            'date_end' => substr($resa['date_end'], 0, 10),
            'start_time' => substr($resa['start_time'], 0, 5),
            'end_time' => substr($resa['end_time'], 0, 5),
            'km_planned' => $resa['km_planned'],
            'id_reason' => $resa['id_reason'],
            'id_company_car' => $car['name'] . " (" . $car['model'] . ")",
            'facility' => $api->table_get("facility", array('id_facility' => ($car ['id_facility'])))[0] ['name'],
            'id_user' => $id_user
          );

          // On regarde l'état des lieux
          $state = $api->table_get("state", array('id_state' => $resa['id_state']))[0];
          $temp_state = [];
          if($state['front'] == NULL) { // Si il n'a pas ete fait, on affiche le formulaire
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
              return $this->redirectToRoute('admin_cars');
            }

            $temp_state['form'] = $state_form->createView();
          } else { // Si il a deja ete fait on affiche le resultat
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
          array_push($resa_car, $temp_resa); // On l'ajoute a la liste

          if(!$temp_resa['state']['done']) // On enleve le formulaire pour l'extraction
            unset($temp_resa['state']['form']);
          array_push($resa_car_extract, $temp_resa);
        }
      }

      // Formulaire d'ajout d'une réservation
      $reservationCar = new AdminReservationCar();
      $car_form = NULL;
      $car_form = $this->get("form.factory")->createNamedBuilder('car_form', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', $reservationCar, array())
        ->add('id_company_car', ChoiceType::class, array('choices' => $company_car_choices, 'label' => "Voiture"))
        ->add('date_start', DateType::class, array('label' => "Date de départ"))
        ->add('start_time', TimeType::class, array('label' => "Heure de départ"))
        ->add('date_end', DateType::class, array('label' => "Date du retour"))
        ->add('end_time', TimeType::class, array('label' => "Heure du retour"))
        ->add('id_reason', ChoiceType::class, array('choices' => $reason_choices, 'label' => "Raison de l'emprunt"))
        ->add('reason_details', TextType::class, array(
          'label' => "Détails si nécessaire",
          'attr' => array(
            'maxlength' => 200,
            'size' => 50
          )
        ))
        ->add('km_planned', NumberType::class, array(
          'label' => "Kilométrage prévu",
          'attr' => array(
            'maxlength' => 5,
            'size' => 5
          )
        ))
        ->add('id_user', ChoiceType::class, array(
          'label' => "Utilisateur",
          'choices' => $user_choice
        ))
        ->add('add_facility', SubmitType::class, array('label' => 'J\'enregistre la réservation'))
        ->getForm();

      // Pour changer le nombre d'elements par page
      $limit_form = $this->get("form.factory")->createNamedBuilder('change_limit_form')
        ->add('new_limit', NumberType::class, array(
          'label' => false,
          'attr' => array(
            'maxlength' => '5',
            'size' => '5'
          )
        ))
        ->add('subscribe_change', SubmitType::class, array('label' => 'Changer le nombre de réservations par page'))
        ->getForm();

      // Voir directement une reservation precise
      $go_to_form = $this->get("form.factory")->createNamedBuilder('go_to_form')
        ->add('number', NumberType::class, array(
          'label' => false,
          'attr' => array(
            'maxlength' => '5',
            'size' => '5'
          )
        ))
        ->add('subscribe_go_to', SubmitType::class, array('label' => 'Aller directement à cette réservation'))
        ->getForm();

      // On recoit une confirmation d'un formulaire
      if('POST' === $request->getMethod()) {
        $car_form->handleRequest($request);
        $limit_form->handleRequest($request);
        $go_to_form->handleRequest($request);

        // On change la limite
        if($request->request->has('change_limit_form') && $limit_form->isValid()) {
          if($limit_form->getData()['new_limit'] > 0)
            $_SESSION['limit_cars'] = $limit_form->getData()['new_limit'];
          return $this->redirectToRoute('admin_cars');
        }

        // On change l'offset
        if($request->request->has('go_to_form') && $go_to_form->isValid()) {
          if($go_to_form->getData()['number'] <= $_SESSION['total_cars']) {
            $_SESSION['offset_cars'] = $go_to_form->getData()['number']-1;
          }
          return $this->redirectToRoute('admin_cars');
        }

        // On crée la reservation et on l'ajoute a la BDD
        if($request->request->has('car_form') && $car_form->isValid()) {
          date_default_timezone_set('Europe/Paris');
          $reservationCar = $car_form->getData();
          $new_resa = array(
            'date_start' => date_format($reservationCar->getDateStart(), 'Y-m-d'),
            'date_end' => date_format($reservationCar->getDateEnd(), 'Y-m-d'),
            'start_time' => $reservationCar->getStartTime()->format('H:i'),
            'end_time' => $reservationCar->getEndTime()->format('H:i'),
            'id_reason' => $reservationCar->getIdReason(),
            'reason_details' => $reservationCar->getReasonDetails(),
            'km_start' => NULL,
            'km_end' => NULL,
            'km_planned' => $reservationCar->getKmPlanned(),
            'id_user' => $reservationCar->getIdUser(),
            'id_company_car' => $reservationCar->getIdCompanyCar(),
            'id_state' => NULL
          );

          $api->table_add("resa_car", $new_resa);
        }
      }

      // Pour l'affichage du template
      if($actual_max == 0)
        $actual_min = 0;
      else
        $actual_min = $_SESSION['offset_cars']+1;

      // On charge le template
      return $this->render('admin/admin_cars.html.twig', array(
        'resa_car' => $resa_car,
        'resa_car_extractable' => $resa_car_extract,
        'car_form' => $car_form->createView(),
        'rights' => $_SESSION['rights'],
        'limit_form' => $limit_form->createView(),
        'go_to_form' => $go_to_form->createView(),
        'limit' => $_SESSION['limit_cars'],
        'offset' => $_SESSION['offset_cars'],
        'actual_min' => $actual_min,
        'actual_max' => $actual_max,
        'total' => $_SESSION['total_cars']
      ));
    }

    /**
     * @Route("/admin/cars/delete/{id_resa}", name="delete_resa_car_admin")
     */
    public function delete_resa($id_resa) { // Supprime une réservation
      // On verifie les droits
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = 2;
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $present = false;
      if($rights == 2) {
        $resa = $api->table_get("resa_car", array('id_resa' => $id_resa))[0];
        $car = $api->table_get("company_car", array('id_company_car' => $resa['id_company_car']))[0];
        foreach($api->table_get("work", array('id_user' => $_SESSION['id_user'])) as $work) {
          if($work['id_facility'] == $car['id_facility']) {
            $present = true;
            break;
          }
        }
      } else
        $present = true;

      // On verifie que l'admin a les droits sur l'etablissement concerné
      if(!$present)
        return $this->redirectToRoute('accueil');

      // On envoie un e-mail pour prévenir l'utilisateur
      $email = $api->table_get("user", array('id_user' => $resa['id_user']))[0]['email'];
      $mail_body = array(
        'email' => $email,
        'subject' => "Annulation de votre réservation n°" . $id_resa,
        'html' => "<p>Votre réservation de voiture décrite ci-dessous a été supprimée par l'Administrateur " . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . "</p>
        <ul>
          <li>Début: " . $resa['date_start'] . " à " . $resa['start_time'] . "</li>
          <li>Fin: " . $resa['date_end'] . " à " . $resa['end_time'] . "</li>
          <li>Raison: " . $resa['id_reason'] . "</li>
          <li>Voiture: " . $api->table_get("company_car", array('id_company_car' => $resa['id_company_car']))[0]['name'] . "</li>
        <ul>
        <p>Vous pouvez le contacter à l'adresse <u>" . $_SESSION['email'] . "</u>.</p>
        "
      );

      // On applique les modifications a la BDD
      $api->send_mail($mail_body);
      $api->table_update("state", array('id_resa' => NULL), array('id_state' => $resa['id_state']));
      $api->table_delete("resa_car", array('id_resa' => $id_resa));
      $api->table_delete("state", array('id_state' => $resa['id_state']));
      return $this->redirectToRoute('admin_cars');
    }

    /**
      * @Route("/admin/cars/show/{way}", name="change_offset_car_admin")
      */
    public function change_offset($way) { // Pour changer l'offset (20 suivants / 20 precedents)
      if($way == 1 && ($_SESSION['limit_cars'] + $_SESSION['offset_cars']) < $_SESSION['total_cars']) {
        $_SESSION['offset_cars'] += $_SESSION['limit_cars'];
      } else if($way == -1 && $_SESSION['offset_cars'] != 0) {
        $_SESSION['offset_cars'] -= $_SESSION['limit_cars'];
      } else if($way == 0) {
        $_SESSION['offset_cars'] = 0;
      }
      return $this->redirectToRoute('admin_cars');
    }

    /**
      *
      * @Route("/admin/cars/extract/{data}", name="extract_car_admin")
      */
    public function extract_data($data) { // Extraire les données dans le fichier cars.json
      $fp = fopen('cars.json', 'w');
      fwrite($fp, $data);
      fclose($fp);
      return $this->redirectToRoute('admin_cars');
    }
  }

 ?>
