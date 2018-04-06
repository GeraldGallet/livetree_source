<?php
namespace App\Controller\Form;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class Inscription extends Controller
{

  /**
    * @Route("/reservationvehiculefonction")
    */
  public function new(Request $request)
  {
    // Create a user
    $user = new User();

    $form = $this->createFormBuilder($user)
        ->add('startdatetime', DateTimeType::class)
        ->add('stopdatetime', DateTimeType::class)
        ->add('reason-proposition', TextType::class)
        ->add('reason-description', TextType::class)
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
        'form' => $form->createView(),
    ));
  }
}

?>
