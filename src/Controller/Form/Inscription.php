<?php
namespace App\Controller\Form;

use App\Entity\User;
use App\Controller\CustomApi;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;

class Inscription extends Controller
{

  /**
    * @Route("/inscription")
    */
  public function new(Request $request)
  {
    // Create a user
    $user = new User();
    $api = new CustomApi();

    $indicative_choices = [];
    foreach($api->phone_indicative_get_all() as $pi) {
      $indicative_choices[$pi['country']] = $pi['indicative'];
    }

    $form = $this->createFormBuilder($user)
        ->add('last_name', TextType::class)
        ->add('first_name', TextType::class)
        ->add('email', EmailType::class)
        ->add('password', PasswordType::class)
        ->add('id_status', ChoiceType::class, array(
          'choices'  => array(
            'Visiteur' => "Visiteur",
            'Etudiant' => 'Etudiant',
            'Salarié' => 'Salarié',
            'Professeur' => 'Professeur'
          )))
        ->add('indicative', ChoiceType::class, array(
          'choices'  => $indicative_choices))
        ->add('phone_number', NumberType::class)
        ->add('subscribe', SubmitType::class, array('label' => 'Je m\'inscris'))
        ->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // $form->getData() holds the submitted values
        // but, the original `$task` variable has also been updated
        $user = $form->getData();
        $api = new CustomApi();

        $domain_name = substr(strrchr($user->getEmail(), "@"), 1);
        $res = $api->domain_get($domain_name);
        if(sizeof($res) == 0)
          return $this->render('forms/inscription.html.twig', array(
              'form' => $form->createView(),
          ));

        $id_facs = [];
        foreach($api->has_domain_get($res[0]['id_domain']) as $has_domain) {
          array_push($id_facs, $has_domain['id_facility']);
        }

        $new_user = NULL;
        $new_user = array(
          'email' => $user->getEmail(),
          'first_name' => $user->getFirstName(),
          'last_name' => $user->getLastName(),
          'password' => $user->getPassword(),
          'phone_number' => $user->getPhoneNumber(),
          'id_status' => $user->getIdStatus(),
          'activated' => false,
          'indicative' => $user->getIndicative()
        );
        $api->user_add($new_user);
        $user_id = $api->user_get($new_user['email'])['id_user'];
        foreach($id_facs as $id) {
          $api->work_add($user_id, $id);
        }
        return $this->redirectToRoute('validation');
    }

    return $this->render('forms/inscription.html.twig', array(
        'form' => $form->createView(),
    ));
  }
}

?>
