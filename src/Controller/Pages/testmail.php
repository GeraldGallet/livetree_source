<?php






$mail = 'coelablivetree@gmail.com'; // Déclaration de l'adresse de destination.

if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // On filtre les serveurs qui présentent des bogues.

{

    $passage_ligne = "\r\n";

}

else

{

    $passage_ligne = "\n";

}

//=====Déclaration des messages au format texte et au format HTML.


$message_html = "<html><head></head><body><b>Salut à tous</b>, voici un e-mail envoyé par un <i>script PHP</i>.</body></html>";
$header = "From: \"livetree\"<mailsender59270@gmail.com>".$passage_ligne;
$header.= "Reply-to: \"livetree\" <mailsender59270@gmail.com".$passage_ligne;
$header.= "MIME-Version: 1.0".$passage_ligne;
$header.= "Content-type: text/html; charset=utf8;".$passage_ligne;
$sujet = "test";

$message= $passage_ligne.$message_html.$passage_ligne;
if(mail($mail, $sujet, $message, $header)){
echo "mail envoyer ";
}
else{
  echo "pasbon ";
}
/**if(mail("mailsender59270@gmail.com", "allo", "j'essaie")){
echo "mail2 envoyer";
}
else{
  echo "pasbon2";
}*/

 ?>
