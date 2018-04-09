<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class History extends Controller
  {
    /**
      * @Route("/profil/historique")
      */
    public function load_history() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      return $this->render('profile/history.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

 ?>
