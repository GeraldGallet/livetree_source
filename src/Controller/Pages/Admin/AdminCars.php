<?php
  namespace App\Controller\Pages\Admin;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class AdminCars extends Controller
  {
    /**
      * @Route("/admin/voitures")
      */
    public function load_admin_cars() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      return $this->render('admin/admin_cars.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

 ?>
