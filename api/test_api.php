<?php
  echo "Hello TEST API ;)\n";
  $mail = "gerald.gallet@isen.yncrea.fr";
  echo file_get_contents('http://localhost:3000/user/' . $mail);

?>
