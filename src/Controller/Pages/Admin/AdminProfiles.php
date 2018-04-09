<?php
  namespace App\Controller\Pages\Admin;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class AdminProfiles extends Controller
  {
    /**
      * @Route("/admin/profils")
      */
    public function load_admin_profiles() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      return $this->render('admin/admin_profiles.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

 ?>
