<?php
  interface api_interface
  {
    public function user_get($mail);
    public function user_delete($mail);
    public function user_change_password($mail, $newpassword);
  }


  class API implements api_interface {
    private $url = 'http://localhost:3000/';

    /* User-related functions */

    // Gives the DB line of specified user
    public function user_get($mail) {
      $request = 'get_user/';

      $ch = curl_init($this->url . $request);
      // We encode the body to send the email
      $data = array(
        'email' => 'robin.poiret@isen.yncrea.fr'
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

    // Deletes a specified user
    public function user_delete($user) {

    }

    // Changes the password of user
    public function user_change_password($mail, $newpassword) {

    }


  }
?>
