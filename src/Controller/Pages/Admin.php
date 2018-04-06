<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Admin extends Controller
  {
    /**
      * @Route("/admin")
      */
    public function load_admin() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      return $this->render('admin/admin.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

 ?>
