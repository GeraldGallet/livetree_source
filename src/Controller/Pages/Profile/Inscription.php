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


//Classes qui contrôle le formulaire d'inscription
class Inscription extends Controller
{

  /**
    * @Route("/inscription")
    */
  public function new(Request $request)
  {
    //On vérifie les droits de l'utilsateur
    if(!isset($_SESSION))
      session_start();

    if(isset($_SESSION['id_user']))
      return $this->redirectToRoute('accueil');


    // On crée un nouveau utilisateur
    $user = new User();
    //L'interface pour l'API
    $api = new CustomApi();
    //On récupère le numéro du pays pour se renseignés
    $indicative_choices = [];
    foreach($api->table_get_all("phone_indicative") as $pi) {
      $indicative_choices[$pi['country']] = $pi['indicative'];
    }
    //On crée le formulaire d'inscription
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
        ->add('subscribe', SubmitType::class, array(
          'label' => 'Je m\'inscris',
          'attr' => [
            'class' => "btn btn-outline-secondary",
            ]));
    $form = $form->getForm();
    $form->handleRequest($request);
    //Si le formulaire est valide
    if ($form->isSubmitted() && $form->isValid())  {
        $user = $form->getData();
        $api = new CustomApi();//L'interface pour l'API
        if ($user -> getPassword() != $user -> getPasswordConfirmation()) {//On vérifie que le mot et sa confirmation correspondent
            return $this->render('forms/inscription.html.twig', array(//Sinon on lui affiche une erreur
              'form' => $form->createView(),
              'error' => "Les 2 mots de passe doivent être identiques !",
              'state' => "Subscribe"
            ));
        }

        $domain_name = substr(strrchr($user->getEmail(), "@"), 1);//On vérifie que le domaine de l'e-mail correspond avec celle dans notre BDD
        $res = $api->table_get("domain", array('domain' => $domain_name));
        if(sizeof($res) == 0)//Sinon on lui indique qu'il ne peut pas avec cet adresse e-mail
          return $this->render('forms/inscription.html.twig', array(
              'form' => $form->createView(),
              'error' => "Votre e-mail n'est pas enregistrée sur ce site",
              'state' => "Subscribe"
          ));

        $id_facs = [];//L'id des établissements
        foreach($api->table_get("has_domain", array('id_domain' => $res[0]['id_domain'])) as $has_domain) { //On récupère l'etablissement correspondant avec le nom de domaine
          array_push($id_facs, $has_domain['id_facility']);
        }

        $passwordInput= $user -> getPassword();
        $user -> setPassword(password_hash($passwordInput,PASSWORD_DEFAULT));//On hash+salt le mot de passe avant de le rentrer dans la BDD

        if($user->getIdStatus() == "Visiteur") { //Si le statut de l'utilisateur est visiteur
          $visiting = true; // Dans l'affichage on rajoute un champ pour renseigner l'e-mail de son référent
          $validation_email = $user->getReferentEmail();
        } else {//sinon on affiche rien de plus
          $visiting = false;
          $validation_email = $user->getEmail();
        }
        //On ajoute les valeurs renseignées à user pour crée un utilisateur
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
        $user_id = $api->table_add("user", $new_user);//on l'ajout notre nouvel utilisateur à notre BDD

        foreach($id_facs as $id) {
          $api->table_add("work", array('id_user' => $user_id, 'id_facility' => $id));//On récupère les id des établissement corespondants à l'utilisateur
        }

        date_default_timezone_set('Europe/Paris');
        $expirationDate = new DateTime("now");
        $expirationDate->modify("+1 hour");
        $token =  substr(bin2hex(random_bytes(40)), 0, 10);

        $new_token = array(//On crée un token avec 10 charactères aléatoires, une date d'expiration et id de l'utilisateur qui crée son compte
          'token' => $token,
          'email' => $validation_email,
          'expiration_time' => date_format($expirationDate, 'Y-m-d H:i:s'),
          'id_user' => $user_id
        );

        $link = "http://localhost:8000/validation/" . $token; //On crée le lien permettant la validation du compte avec le token
        $mail_body = array( // On génére le mail avec le lien
          'email' => $validation_email,
          'subject' => "Validation de votre compte",
          'html' => "<p>Vous pouvez valider votre compte Live Tree en cliquant sur <u><a href=\"" . $link . "\">ce lien</a></u></p>"
        );


        $api->table_add("email_validate", $new_token);// On ajoute le token dans la BDD
        $api->send_mail($mail_body);//On envoie le mail avec NodeMailer
        return $this->render('forms/inscription.html.twig', array(//On renvoie vers inscription avec le message  que la démarche à fonctionné
            'email' => $validation_email,
            'visiting' => $visiting,
            'state' => "Validation"
        ));
    }

    return $this->render('forms/inscription.html.twig', array(//On affiche le formulaire
        'form' => $form->createView(),
        'error' => NULL,
        'state' => "Subscribe"
    ));

  }

}

?>
