<?php
  namespace App\Controller;

  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class Acceuil
  {
    /**
      * @Route("/acceuil")
      */
    public function acceuil() {
      $number = mt_rand(0, 100);
      return new Response(
          '<html><body>Lucky number: '.$number.'</body></html>'
      );
    }
  }

?>
