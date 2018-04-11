<?php
// composer require mailer
  namespace App\Controller\Pages;

  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Annotation\Route;

  class testmail extends Controller{

    /**
      * @Route("/test", name="test_mail")
      */
      public function indexAction( \Swift_Mailer $mailer)
      {
        $name='robin';
        echo "$name";
          $message = (new \Swift_Message('Hello Email'))
              ->setFrom('mailsender59270@gmail.com')
              ->setTo('mailsender59270@gmail.com')
              ->setBody(
                  $this->renderView(
                      // templates/emails/registration.html.twig
                      'test_mail.html.twig',
                      array('name' => $name)
                  ),
                  'text/html'
              )

          ;

        if ($mailer->send($message))
        {
          echo "done";
        }else
        {
          echo "fail";
        }


          return $this->render('test_mail.html.twig');
      }
  }

  ?>
