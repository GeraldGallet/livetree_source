<?php
namespace App\Controller\Form;

use App\Entity\Reservation;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Routing\Annotation\Route;

class ReservationVoitureFonction extends Controller
{

    /**
     * @Route("/reserver/{slug}",name="app_form_ReservationVoitureFonction_new")
     */
    public function new($slug, Request $request)
    {
        // Create a user
        $user = new Reservation();

        $form = $this->createFormBuilder($user)
            ->add('startdatetime', DateTimeType::class)
            ->add('stopdatetime', DateTimeType::class)
            ->add('reason_proposition', TextType::class)
            ->add('reason_description', TextType::class)
            ->add('submit_book', SubmitType::class, array('label' => 'Je réserve'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $user = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($task);
            // $entityManager->flush();

            return $this->redirectToRoute('inscription_success');
        }
        return $this->render('forms/reservationVehicules.html.twig', array(

            'slug'=>$slug,
            'form' => $form->createView(),

        ));
    }

    /**
     * @Route("/reserver/{slug}/add", name="app_form_ReservationVoitureFonction_numberOfReservation", methods={"POST"})
     */
    public function toggleNumberOfReservation($slug)
    {


        if(file_exists('compteur_visites.txt'))
        {
            $compteur_f = fopen('compteur_visites.txt', 'r+') or die ("unable to open file");
            $compte = fgets($compteur_f);
        }
        else
        {
            $compteur_f = fopen('compteur_visites.txt', 'w+');
            $compte = 0;
        }

        $compte++;
        fseek($compteur_f, 0);
        fputs($compteur_f, $compte);
        fclose($compteur_f);
        return new JsonResponse(['hearts' => $compte]);


    }
    /**
     * @Route("/reserver/{slug}/reset", name="app_form_ReservationVoitureFonction_numberOfReservationReset", methods={"POST"})
     */
    public function toggleNumberOfReservationReset($slug)
    {


        if(file_exists('compteur_visites.txt'))
        {
            $compteur_f = fopen('compteur_visites.txt', 'a+');
            $compte = fgets($compteur_f);

        }
        else
        {
            $compteur_f = fopen('compteur_visites.txt', 'w+');
            $compte = 0;
        }
        $compte=0;
        $compteur_f = fopen('compteur_visites.txt', 'w+');
        fputs($compteur_f, $compte);
        fclose($compteur_f);

        return new JsonResponse(['hearts' => $compte]);


    }

}

?>
