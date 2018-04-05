<?php
  namespace App\Controller;

  use App\Entity\PersonalCar;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Form\Extension\Core\Type\TextType;
  use Symfony\Component\Form\Extension\Core\Type\DateType;
  use Symfony\Component\Form\Extension\Core\Type\SubmitType;

  class AddPersonalCar extends Controller
  {
      public function new(Request $request)
      {
          // creates a task and gives it some dummy data for this example
          $personalcar = new PersonalCar();
          $personalcar->setName('Voiture perso');
          $personalcar->setModel('Tesla Model S');
          $personalcar->setPower('20 kWh');

          $form = $this->createFormBuilder($personalcar)
              ->add('name', TextType::class)
              ->add('model', TextType::class)
              ->add('power', TextType::class)
              ->add('add car', SubmitType::class, array('label' => 'Add a new personal car'))
              ->getForm();

          return $this->render('default/new.html.twig', array(
              'form' => $form->createView(),
          ));
      }
  }

?>
