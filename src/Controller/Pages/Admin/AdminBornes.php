<?php
  namespace App\Controller\Pages\Admin;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class AdminBornes extends Controller
  {
    /**
      * @Route("/admin/bornes")
      */
    public function load_admin_bornes() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      return $this->render('admin/admin_bornes.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

 ?>
