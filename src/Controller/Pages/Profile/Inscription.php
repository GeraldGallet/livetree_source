<?php
namespace App\Controller\Pages\Profile;

use App\Entity\User;
use App\Controller\CustomApi;

use \DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
        ->add('subscribe', SubmitType::class, array('label' => 'Je m\'inscris'));
    $form = $form->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())  {
        $user = $form->getData();
        $api = new CustomApi();
        if ($user -> getPassword() != $user -> getPasswordConfirmation()) {
            return $this->render('forms/inscription.html.twig', array(
              'form' => $form->createView(),
              'error' => "Les 2 mots de passe doivent être identiques !",
              'state' => "Subscribe"
            ));
        }

        $domain_name = substr(strrchr($user->getEmail(), "@"), 1);
        $res = $api->table_get("domain", array('domain' => $domain_name));
        if(sizeof($res) == 0)
          return $this->render('forms/inscription.html.twig', array(
              'form' => $form->createView(),
              'error' => "Votre e-mail n'est pas enregistrée sur ce site",
              'state' => "Subscribe"
          ));

        $id_facs = [];
        foreach($api->table_get("has_domain", array('id_domain' => $res[0]['id_domain'])) as $has_domain) {
          array_push($id_facs, $has_domain['id_facility']);
        }

        $passwordInput= $user -> getPassword();
        $user -> setPassword(password_hash($passwordInput,PASSWORD_DEFAULT));

        if($user->getIdStatus() == "Visiteur") {
          $visiting = true;
          $validation_email = $user->getReferentEmail();
        } else {
          $visiting = false;
          $validation_email = $user->getEmail();
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
        $user_id = $api->table_add("user", $new_user);

        foreach($id_facs as $id) {
          $api->table_add("work", array('id_user' => $user_id, 'id_facility' => $id));
        }

        date_default_timezone_set('Europe/Paris');
        $expirationDate = new DateTime("now");
        $expirationDate->modify("+1 hour");
        $token =  substr(bin2hex(random_bytes(40)), 0, 10);

        $new_token = array(
          'token' => $token,
          'email' => $validation_email,
          'expiration_time' => date_format($expirationDate, 'Y-m-d H:i:s'),
          'id_user' => $user_id
        );

        $link = "http://localhost:8000/validation/" . $token;
        $mail_body = array(
          'email' => $validation_email,
          'subject' => "Validation de votre compte",
          'html' => "<p>Vous pouvez valider votre compte Live Tree en cliquant sur <u><a href=\"" . $link . "\">ce lien</a></u></p>"
        );


        $api->table_add("email_validate", $new_token);
        $api->send_mail($mail_body);
        return $this->render('forms/inscription.html.twig', array(
            'email' => $validation_email,
            'visiting' => $visiting,
            'state' => "Validation"
        ));
    }

    return $this->render('forms/inscription.html.twig', array(
        'form' => $form->createView(),
        'error' => NULL,
        'state' => "Subscribe"
    ));

  }

}

?>
