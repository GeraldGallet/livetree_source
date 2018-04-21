<?php
  namespace App\Controller\Pages\Admin;

  use App\Controller\CustomApi;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class ProfileAdmin extends Controller
  {
    /**
      * @Route("/admin/profils", name="admin_profiles")
      */
    public function load_admin_profiles() {
      if(!isset($_SESSION))
        session_start();

      if(!isset($_SESSION['id_user']))
        return $this->redirectToRoute('accueil');

      $rights = $_SESSION['rights'];
      if($rights < 2)
        return $this->redirectToRoute('accueil');

      $api = new CustomApi();
      $profiles = [];

      foreach($api->table_get_all("user") as $user) {
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

      return $this->render('admin/admin_profiles.html.twig', array(
            'profiles' => $profiles,
            'rights' => $_SESSION['rights']
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
      $api->table_delete("resa_borne", array('id_user' => $id_user));
      $api->table_delete("resa_car", array('id_user' => $id_user));
      $api->table_delete("personal_car", array('id_user' => $id_user));
      $api->table_delete("has_access", array('id_user' => $id_user));
      $api->table_delete("work", array('id_user' => $id_user));
      $api->table_delete("user", array('id_user' => $id_user));

      return $this->redirectToRoute('admin_profiles');
    }


  }

 ?>
