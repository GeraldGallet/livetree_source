<?php
namespace App\Controller\Form;

use App\Entity\User;
use App\Controller\CustomApi;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Annotation\Route;


class Inscription extends Controller
{

  /**
    * @Route("/inscription")
    */
  public function new(Request $request)
  {
    if(!isset($_SESSION))
      session_start();

    if(isset($_SESSION['id_user']))
      return $this->redirectToRoute('accueil');


    // Create a user
    $user = new User();
    $api = new CustomApi();

    $indicative_choices = [];
    foreach($api->table_get_all("phone_indicative") as $pi) {
      $indicative_choices[$pi['country']] = $pi['indicative'];
    }

    $form = $this->createFormBuilder($user)
        ->add('last_name', TextType::class, array('label' => 'Nom: '))
        ->add('first_name', TextType::class, array('label' => 'Prenom: '))
        ->add('email', EmailType::class, array('label' => 'Email: '))
        ->add('password', PasswordType::class, array('label' => 'Mot de passe: '))
        ->add('password_confirmation', PasswordType::class, array('label' => 'Confirmer votre mot de passe: '))
        ->add('id_status', ChoiceType::class, array(
          'label' => 'Statut: ',
          'choices'  => array(
            'Etudiant' => 'Etudiant',
            'Salarié' => 'Salarié',
            'Professeur' => 'Professeur',
            'Visiteur' => "Visiteur"
          )))
        ->add('referent_email', TextType::class, array('label' => "Email de votre contact"))
        ->add('indicative', ChoiceType::class, array(
          'choices'  => $indicative_choices))
        ->add('phone_number', NumberType::class, array('label' => 'Téléphone: '))
        ->add('subscribe', SubmitType::class, array('label' => 'Je m\'inscris'))
        ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
          $user = $event->getData();
          $form = $event->getForm();

          if($user->getIdStatus() == "Visiteur") {
            $form->add('referent_email', EmailType::class, array('label' => 'Email de votre contact: '));
          }
        });
    $form = $form->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())  {
        $user = $form->getData();
        $api = new CustomApi();
        if ($user -> getPassword() != $user -> getPasswordConfirmation()) {
            return $this->render('forms/inscription.html.twig', array(
              'form' => $form->createView(),
              'error' => "Les 2 mots de passe doivent être identiques !"
            ));
        }

        $domain_name = substr(strrchr($user->getEmail(), "@"), 1);
        $res = $api->table_get("domain", array('domain' => $domain_name));
        if(sizeof($res) == 0)
          return $this->render('forms/inscription.html.twig', array(
              'form' => $form->createView(),
              'error' => "Votre e-mail n'est pas enregistrée sur ce site"
          ));

        $id_facs = [];
        foreach($api->table_get("has_domain", array('id_domain' => $res[0]['id_domain'])) as $has_domain) {
          array_push($id_facs, $has_domain['id_facility']);
        }


        $passwordInput= $user -> getPassword();
        $user -> setPassword(password_hash($passwordInput,PASSWORD_DEFAULT));

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
        $user_id = $api->table_add("user", $new_user);
        //$user_id = $api->table_get("user", array('email' => $new_user['email']))[0]['id_user'];
        foreach($id_facs as $id) {
          $api->table_add("work", array('id_user' => $user_id, 'id_facility' => $id));
        }
        return $this->redirectToRoute('validation');
    }

    return $this->render('forms/inscription.html.twig', array(
        'form' => $form->createView(),
        'error' => NULL
    ));
  }
}

?>
