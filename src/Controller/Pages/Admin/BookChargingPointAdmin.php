<?php
  namespace App\Controller\Pages\Admin;

  use App\Entity\BookChargingPointEntity;
  use App\Entity\FiltreReservationBorne;
  use App\Controller\CustomApi;
  use \DateTime;

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
  use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
    use Symfony\Component\Form\Extension\Core\Type\RangeType;


  class BookChargingPointAdmin extends Controller
  {
    /**
      * @Route("/admin/bornes", name="admin_bornes")
      */
    public function load_admin_bornes(Request $request) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $place_choices = [];
      $resas = [];
      $user_choice = [];

      if($rights == 3) {
        foreach($api->table_get_all("place") as $place) {
          $place_choices[$place['name']] = $place['id_place'];
        }
      } else {
        foreach($api->table_get("has_access", array('id_user' => $_SESSION['id_user'])) as $acc) {
          $place_choices[$api->table_get("place", array('id_place' => $acc['id_place']))[0]['name']] = $acc['id_place'];
        }
      }

      foreach($place_choices as $id) {
        foreach($api->table_get("has_access", array('id_place' => $id)) as $acc) {
          $user = $api->table_get("user", array('id_user' => $acc['id_user']))[0];
          $user_choice[$user['email']] = $user['id_user'];
        }
      }

      $resa_borne = new BookChargingPointEntity();
      $resa_form = $this->get("form.factory")->createNamedBuilder('adding_form', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', $resa_borne, array())
        ->add('id_place', ChoiceType::class, array(
          'choices' => $place_choices,
          'label' => "Lieu: "
        ))
        ->add('start_date', DateTimeType::class,array(
          'label' => "Date et heure d'arrivée: ",
          'date_widget' => 'single_text',
          'widget' => 'choice',
          'minutes' => [0, 15, 30, 45]
        ))
        ->add('end_date', TimeType::class,array(
          'label' => "Heure de départ: ",
          'widget' => 'choice',
          'minutes' => [0, 15, 30, 45]
        ))
        ->add('charge', RangeType::class, [
                   'attr' => [
                      "data-provide" => "slider",
                      "data-slider-ticks" => "[1, 2, 3, 4]",
                      "data-slider-ticks-labels" => '["short", "medium", "long", "xxl"]',
                      "min" => 1,
                      "max" => 100,
                      "step" => 1,
                      "value" => 100,
                   ]
               ]
         )
         ->add('id_user', ChoiceType::class, array(
           'label' => "Utilisateur: ",
           'choices' => $user_choice
         ))
         ->add('id_personal_car', ChoiceType::class, array(
           'choices' => array('2' => 2),
           'label' => "Voiture à recharger: "
         ))
         ->add('subscribe', SubmitType::class, array('label' => 'Je réserve'))
         ->getForm();

      $filter = new FiltreReservationBorne();
      $filter_form = $this->get("form.factory")->createNamedBuilder('filter_form', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', $filter, array())
      ->add('date_start', DateTimeType::class,array(
        'date_widget' => 'single_text',
        'widget' => 'choice',
        'data' => new DateTime("now"),
        'minutes' => [0, 15, 30, 45]
      ))
      ->add('date_end', TimeType::class, array('widget' => 'single_text'))
      ->add('charge', RangeType::class, [
                 'attr' => [
                    "data-provide" => "slider",
                    "data-slider-ticks" => "[1, 2, 3, 4]",
                    "data-slider-ticks-labels" => '["short", "medium", "long", "xxl"]',
                    "min" => 1,
                    "max" => 100,
                    "step" => 1,
                    "value" => 100,
                 ]
             ]
       )
      ->add('id_user', NumberType::class)
      ->add('id_place', ChoiceType::class, array(
        'choices'  => $place_choices))
      ->add('filter_resa', SubmitType::class, array('label' => 'Filtrer'))
      ->getForm();

      if('POST' === $request->getMethod()) {
        $resa_form->handleRequest($request);
        $filter_form->handleRequest($request);

        if($request->request->has('adding_form') && $resa_form->isValid()) {
          $resa_borne = $resa_form->getData();
          $start_date = $resa_borne->getStartDate();
          $end_date = new DateTime();
          $end_date->setDate($start_date->format('Y'), $start_date->format('m'), $start_date->format('d'));
          $end_date->setTime($resa_borne->getEndDate()->format('H'), $resa_borne->getEndDate()->format('i'), $resa_borne->getEndDate()->format('s'));

          $api->table_add("resa_borne", array(
            'date_creation' => date_format(new DateTime("now"), 'Y-m-d'),
            'date_last_modification' => date_format(new DateTime("now"), 'Y-m-d'),
            'start_date' => $start_date->format('Y-m-d H:i:s'),
            'end_date' => $end_date->format('Y-m-d H:i:s'),
            'charge' => $resa_borne->getCharge(),
            'id_user' => $resa_borne->getIdUser(),
            'id_place' => $resa_borne->getIdPlace(),
            'id_personal_car' => $resa_borne->getIdPersonalCar()
          ));

          return $this->redirectToRoute('admin_bornes');
        }

        if($request->request->has('filter_form') && $filter_form->isValid()) {
          $filter = $filter_form->getData();
          if($filter->getDateStart() == NULL)
            $date = NULL;
          else
            $date = date_format($filter->getDateStart(), 'Y-m-d-H-i');

          if($filter->getDateEnd() == NULL)
            $end = NULL;
          else
            $end = $filter->getDateEnd()->format('H:i');

          $filter_options = array(
            'id_place' => $filter->getIdPlace(),
            'start_date' => $date,
            'end_date' => $end,
            'charge' => $filter->getCharge(),
            'id_user' => $filter->getIdUser(),
          );

          foreach($filter_options as $key => $value) {
            if($value == NULL)
            unset($filter_options[$key]);
          }
          $resas = $api->table_get("resa_borne", $filter_options);
          return $this->render('admin/admin_bornes.html.twig', array(
            'resa_bornes' => $resas,
            'resa_form' => $resa_form->createView(),
            'filter_form' => $filter_form->createView(),
            'rights' => $_SESSION['rights']
          ));
        }
      }

      return $this->render('admin/admin_bornes.html.twig', array(
            'resa_bornes' => $resas,
            'resa_form' => $resa_form->createView(),
            'filter_form' => $filter_form->createView(),
            'rights' => $_SESSION['rights']
      ));
    }

    /**
     * @Route("/admin/bornes/delete/{id_resa}", name="delete_resa_borne_admin")
     */
    public function delete_resa($id_resa) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = 2;
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $present = false;
      $api = new CustomApi();
      if($rights == 2) {
        $resa = $api->table_get("resa_borne", array('id_resa' => $id_resa))[0];
        foreach($api->table_get("has_access", array('id_user' => $_SESSION['id_user'])) as $acc) {
          if($acc['id_place'] == $resa['id_place']) {
            $present = true;
            break;
          }
        }
      } else
        $present = true;

      if(!$present)
        return $this->redirectToRoute('accueil');

      $api->table_delete("resa_borne", array('id_resa' => $id_resa));
      return $this->redirectToRoute('admin_bornes');
    }
  }

 ?>
