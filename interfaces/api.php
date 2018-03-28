<?php
  interface api_interface
  {
    public function user_get($mail);
    public function user_add($user);
    public function user_delete($mail);
    public function user_change_password($mail, $newpassword);
  }


  class CustomAPI implements api_interface {
    private $url = 'http://localhost:3000/';

    /* User related functions */
    // Gives the DB line of specified user
    public function user_get($mail) {
      $request = 'user/get/';

      $ch = curl_init($this->url . $request);
      // We encode the body to send the email
      $data = array(
        'email' => $mail
      );
      $data = json_encode($data);

      // We use a POST request for security reason
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

      $data = curl_exec($ch);
      $data = json_decode($data, true);
      return $data['response'][0];
    }

    // Adds a new user
    public function user_add($user) {
      $user = json_encode($user);

      $ch = curl_init($this->url . 'user/add/');
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $user);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      $result = curl_exec($ch);
    }

    // Deletes a specified user
    public function user_delete($mail) {
      $ch = curl_init($this->url . 'user/');
      $data = array(
        'email' => $mail
      );
      $data = json_encode($data);

      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
      $result = curl_exec($ch);
    }

    // Changes the password of user
    public function user_change_password($mail, $newpassword) {
      $data = array(
        'email' => $mail,
        'password' => $newpassword
      );
      $data = json_encode($data);

      $ch = curl_init($this->url . 'user/password/');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      $result = curl_exec($ch);
      curl_close($ch);
    }


  }
?>
