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
      $resa = array(
        'id_resa' => -1,
        'date_resa' => 0,
        'start_time' => 1,
        'end_time' => 2,
        'charge' => 3,
        'id_place' => 4,
        'id_user' => 5
      );

      $resas = [$resa, $resa, $resa, $resa];
      return $this->render('admin/admin_bornes.html.twig', array(
            'resa_bornes' => $resas
      ));
    }

    /**
     * @Route("/admin/bornes/delete/{id_resa}", name="delete_resa_borne")
     */
    public function delete_resa($id_resa) {
      // Delete a reservation
    }
  }

 ?>
