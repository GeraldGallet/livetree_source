<?php
  namespace App\Controller;

  use App\Controller\CustomApi;
  use \DateTime;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class TestNico extends Controller {
    /**
      * @Route("/nico", name="nico")
      */
    function test() {
      return $this->render('Nico/LIVETREESITE/home.html.twig');
    }
  }
?>
