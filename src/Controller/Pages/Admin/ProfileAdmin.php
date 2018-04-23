<?php
  namespace App\Controller\Pages\Admin;

  use App\Controller\CustomApi;
  use App\Entity\User;
  use App\Entity\ChangeOffsetEntity;
  use \DateTime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\DateType;
  use Symfony\Component\Form\Extension\Core\Type\EmailType;
  use Symfony\Component\Form\Extension\Core\Type\PasswordType;
  use Symfony\Component\Form\Extension\Core\Type\NumberType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;
  use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

  class ProfileAdmin extends Controller
  {
    /**
      * @Route("/admin/profils", name="admin_profiles")
      */
    public function load_admin_profiles(Request $request) {
      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $indicative_choices = [];
      foreach($api->table_get_all("phone_indicative") as $pi)
        $indicative_choices[$pi['country']] = $pi['indicative'];

      $facility_choices = [];
      $facility_options = "AND (";
      if($rights == 2) {
        foreach($api->table_get("work", array('id_user' => $_SESSION['id_user'])) as $work) {
          array_push($facility_choices, $work['id_facility']);
          $facility_options .= 'id_facility = ' . $work['id_facility'] . " OR ";
        }
      } else {
        foreach($api->table_get_all("facility") as $fac) {
          array_push($facility_choices, $fac['id_facility']);
          $facility_options .= 'id_facility = ' . $work['id_facility'] . " OR ";
        }
      }
      $facility_options .= "1) ";

      $profiles = [];
      $_SESSION['total_profiles'] = sizeof($api->table_get("work", array(), $facility_options));

      foreach($facility_choices as $id_fac) {
        $options = [$facility_options, "LIMIT " . $_SESSION['limit_profiles'] . " ", "OFFSET " . $_SESSION['offset_profiles'] . " "];
        $res = $api->table_get("work", array(), $options);
        $actual_max = $_SESSION['offset_profiles']+sizeof($res);
        foreach($res as $work) {
            $user = $api->table_get("user", array('id_user' => $work['id_user']))[0];
            array_push($profiles, array(
              'id_user' => $user['id_user'],
              'email' => $user['email'],
              'last_name' => $user['last_name'],
              'first_name' => $user['first_name'],
              'activated' => $user['activated'],
              'phone_number' => $user['phone_number'],
              'indicative' => $user['indicative'],
              'id_status' => $user['id_status']
            ));
        }
      }

      $user = new User();
      $form = $this->get("form.factory")->createNamedBuilder('add_user_form', 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', $user, array())
          ->add('last_name', TextType::class, array(
            'label' => 'Nom: ',
            'attr' => array(
              'maxlength' => '50'
            )
          ))
          ->add('first_name', TextType::class, array(
            'label' => 'Prenom: ',
            'attr' => array(
              'maxlength' => '50'
            )
          ))
          ->add('email', EmailType::class, array(
            'label' => 'Email: ',
            'attr' => array(
              'maxlength' => '100'
            )
          ))
          ->add('password', PasswordType::class, array(
            'label' => 'Mot de passe: ',
            'attr' => array(
              'maxlength' => '100'
            )
          ))
          ->add('password_confirmation', PasswordType::class, array(
            'label' => 'Confirmer votre mot de passe: ',
            'attr' => array(
              'maxlength' => '100'
            )
          ))
          ->add('id_status', ChoiceType::class, array(
            'label' => 'Statut: ',
            'choices'  => array(
              'Etudiant' => 'Etudiant',
              'Salarié' => 'Salarié',
              'Professeur' => 'Professeur',
              'Visiteur' => "Visiteur",
              'Admin' => "Admin"
            )))
          ->add('indicative', ChoiceType::class, array(
            'choices'  => $indicative_choices))
          ->add('phone_number', NumberType::class, array(
            'label' => 'Téléphone: ',
            'attr' => array(
              'maxlength' => '10',
              'size' => '10'
            )
          ))
          ->add('subscribe_add', SubmitType::class, array('label' => 'Créer le compte'));
      $form = $form->getForm();

      $limit_form = $this->get("form.factory")->createNamedBuilder('change_limit_form')
        ->add('new_limit', NumberType::class, array(
          'label' => false,
          'attr' => array(
            'maxlength' => '5',
            'size' => '5'
          )
        ))
        ->add('subscribe_change', SubmitType::class, array('label' => 'Changer le nombre de profil par page'))
        ->getForm();

      $go_to_form = $this->get("form.factory")->createNamedBuilder('go_to_form')
        ->add('number', NumberType::class, array(
          'label' => false,
          'attr' => array(
            'maxlength' => '5',
            'size' => '5'
          )
        ))
        ->add('subscribe_go_to', SubmitType::class, array('label' => 'Aller directement à ce profil'))
        ->getForm();

      if('POST' === $request->getMethod()) {
        $form->handleRequest($request);
        $limit_form->handleRequest($request);
        $go_to_form->handleRequest($request);

        if($request->request->has('add_user_form') && $form->isValid()) {
          $user = $form->getData();

          if($user->getPassword() != $user->getPasswordConfirmation()) {
            return $this->render('admin/admin_profiles.html.twig', array(
                  'profiles' => $profiles,
                  'rights' => $_SESSION['rights'],
                  'form' => $form->createView(),
                  'error' => "Les 2 mots de passe ne correspondent pas !",
                  'actual_min' => $_SESSION['offset_profiles']+1,
                  'actual_max' => $actual_max,
                  'total' => $_SESSION['total_profiles'],
                  'limit_form' => $limit_form->createView(),
                  'go_to_form' => $go_to_form->createView(),
                  'limit' => $_SESSION['limit_profiles']
            ));
          }

          $domain_name = substr(strrchr($user->getEmail(), "@"), 1);
          $res = $api->table_get("domain", array('domain' => $domain_name));
          if(sizeof($res) == 0)
            return $this->render('admin/admin_profiles.html.twig', array(
                  'profiles' => $profiles,
                  'rights' => $_SESSION['rights'],
                  'form' => $form->createView(),
                  'error' => "Ce nom de domaine n'est pas enregistré sur le site !",
                  'actual_min' => $_SESSION['offset_profiles']+1,
                  'actual_max' => $actual_max,
                  'total' => $_SESSION['total_profiles'],
                  'limit_form' => $limit_form->createView(),
                  'go_to_form' => $go_to_form->createView(),
                  'limit' => $_SESSION['limit_profiles']
            ));

          $id_facs = [];
          foreach($api->table_get("has_domain", array('id_domain' => $res[0]['id_domain'])) as $has_domain) {
            array_push($id_facs, $has_domain['id_facility']);
          }

          $passwordInput = $user->getPassword();
          $user -> setPassword(password_hash($passwordInput,PASSWORD_DEFAULT));

          $new_user = NULL;
          $new_user = array(
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'password' => $user->getPassword(),
            'phone_number' => $user->getPhoneNumber(),
            'id_status' => $user->getIdStatus(),
            'activated' => true,
            'indicative' => $user->getIndicative()
          );
          $user_id = $api->table_add("user", $new_user);

          foreach($id_facs as $id) {
            $api->table_add("work", array('id_user' => $user_id, 'id_facility' => $id));
          }
        }

        if($request->request->has('change_limit_form') && $limit_form->isValid()) {
          if($limit_form->getData()['new_limit'] > 0)
            $_SESSION['limit_profiles'] = $limit_form->getData()['new_limit'];
          return $this->redirectToRoute('admin_profiles');
        }

        if($request->request->has('go_to_form') && $go_to_form->isValid()) {
          if($go_to_form->getData()['number'] <= $_SESSION['total_profiles'])
            $_SESSION['offset_profiles'] = $go_to_form->getData()['number']-1;
          return $this->redirectToRoute('admin_profiles');
        }
      }

      return $this->render('admin/admin_profiles.html.twig', array(
            'profiles' => $profiles,
            'rights' => $_SESSION['rights'],
            'form' => $form->createView(),
            'error' => null,
            'actual_min' => $_SESSION['offset_profiles']+1,
            'actual_max' => $actual_max,
            'total' => $_SESSION['total_profiles'],
            'limit_form' => $limit_form->createView(),
            'go_to_form' => $go_to_form->createView(),
            'limit' => $_SESSION['limit_profiles']
      ));
    }

    /**
      * @Route("/admin/profils/give_admin/{id_user}", name="give_admin")
      */
    public function give_admin_rights($id_user) {
      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $api->table_update("user", array('id_status' => "Admin"), array('id_user' => $id_user));
      return $this->redirectToRoute('admin_profiles');
    }

    /**
      * @Route("/admin/profils/activer/{id_user}", name="activate_user_admin")
      */
    public function activate_user($id_user) {
      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $api->table_update("user", array('activated' => true), array('id_user' => $id_user));
      return $this->redirectToRoute('admin_profiles');
    }

    /**
      * @Route("/admin/profils/delete/{id_user}", name="delete_user_admin")
      */
    public function delete_user($id_user) {
      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $currentDate = new DateTime("now");
      $currentDate = date_format($currentDate, 'Y-m-d H:i:s');
      $options = ['AND start_date > \'' . $currentDate . '\' '];
      $api->table_delete("resa_borne", array('id_user' => $id_user), $options);
      $api->table_update("resa_borne", array('id_user' => null, 'id_personal_car' => null), array('id_user' => $id_user));

      $currentDate = new DateTime("now");
      $currentDate = date_format($currentDate, 'Y-m-d');
      $options = ['AND date_end > \'' . $currentDate . '\' '];
      $api->table_delete("resa_car", array('id_user' => $id_user), $options);
      $api->table_update("resa_car", array('id_user' => null), array('id_user' => $id_user));

      $api->table_delete("personal_car", array('id_user' => $id_user));
      $api->table_delete("has_access", array('id_user' => $id_user));
      $api->table_delete("work", array('id_user' => $id_user));
      $api->table_delete("user", array('id_user' => $id_user));

      return $this->redirectToRoute('admin_profiles');
    }

    /**
      * @Route("/admin/profils/show/{way}", name="change_offset_user_admin")
      */
    public function change_offset($way) {
      if($way == 1 && ($_SESSION['limit_profiles'] + $_SESSION['offset_profiles']) < $_SESSION['total_profiles']) {
        $_SESSION['offset_profiles'] += $_SESSION['limit_profiles'];
      } else if($way == -1 && $_SESSION['offset_profiles'] != 0) {
        $_SESSION['offset_profiles'] -= $_SESSION['limit_profiles'];
      } else if($way == 0) {
        $_SESSION['offset_profiles'] = 0;
      }
      return $this->redirectToRoute('admin_profiles');
    }
  }

 ?>
