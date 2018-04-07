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
        return $this->render('forms/inscription.html.twig', array(
            /*'slug' => $slug,
            */
            'form' => $form->createView(),

        ));
    }

    /**
     * @Route("/reserver1", name="app_form_ReservationVoitureFonction_numberOfReservation" )
     */
    public function toggleNumberOfReservation()
    {
        $numberofhearts = 5;
/*        return new JsonResponse(['hearts' => $numberofhearts]);*/
        return new JsonResponse(['hearts' => rand(0,100)]);
/*        * @Route(/reserver/{slug}/number, name="app_form_ReservationVoitureFonction_numberOfReservation" ,methods={POST})*/


    }

}

?>
