<?php
  echo "Hello TEST API ;)\n";
  $mail = "gerald.gallet@isen.yncrea.fr";
  $response = (array) json_decode(file_get_contents('http://localhost:3000/user/' . $mail));
  //$results = (array) json_decode($response["response"]);
  $results = json_decode($response["response"], true);
  $results = $results[0];
  echo $results["first_name"];
  //echo $results;
?>
