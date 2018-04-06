<?php
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Profile extends Controller
  {
    /**
      * @Route("/profil")
      */
    public function load_profile() {
      $first_name = "Gérald";
      $last_name = "Gallet";
      return $this->render('profile/profile.html.twig', array(
            'first_name' => $first_name,
            'last_name' => $last_name
      ));
    }
  }

?>
