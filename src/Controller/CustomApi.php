<?php

  namespace App\Controller;

  interface api_interface
  {
    // Communication functions
    public function api_connect($url);
    public function api_options($connection, $type, $data);
    public function api_close($connection);

    // Status functions
    public function status_get($status);
    public function status_add($status);
    public function status_delete($status);

    // User functions
    public function user_get($mail);
    public function user_add($user);
    public function user_delete($mail);
    public function user_change_password($mail, $newpassword);

    // Personal cars functions
    public function personal_car_get_all($user);
    public function personal_car_get($user, $name);
    public function personal_car_add($car);
    public function personal_car_delete($user, $name);

    // Facilities functions
    public function facility_get_all();
    public function facility_get($name);
    public function facility_add($facility);
    public function facility_delete($name);

  }


  class CustomApi implements api_interface {
    private $url = 'http://localhost:3000/';

    /* Communication with the API related functions */

    // Launches connection with the API
    public function api_connect($url) {
      return curl_init($url);
    }

    // Settles the options of the connection for the desired type of request
    public function api_options($connection, $type, $data) {
      switch($type) {
        case "POST":
          curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
          curl_setopt($connection, CURLOPT_POST, 1);
          curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
          curl_setopt($connection, CURLOPT_RETURNTRANSFER, TRUE);
          break;

        case "DELETE":
          curl_setopt($connection, CURLOPT_POSTFIELDS, $data);
          curl_setopt($connection, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
          curl_setopt($connection, CURLOPT_RETURNTRANSFER, TRUE);
          curl_setopt($connection, CURLOPT_CUSTOMREQUEST, 'DELETE');
          break;

        case "PATCH":
          break;

        default:
          break;
      }

      return $connection;
    }

    // Closes te connection to the API
    public function api_close($connection) {

    }

    /* Status related functions */
    // Gives the DB line of the specified statuses
    public function status_get($status) {
      $ch = $this->api_connect($this->url . 'status/get/');
      // We encode the body to send the email
      $data = array(
        'id_status' => $status
      );
      $data = json_encode($data);

      // We use a POST request for security reason
      $ch = $this->api_options($ch, "POST", $data);
      $data = curl_exec($ch);
      $this->api_close($ch);

      $data = json_decode($data, true);
      return $data['response'][0];
    }

    // Adds a new status to DB
    public function status_add($status) {
      $status = json_encode($status);
      $ch = $this->api_connect($this->url . 'status/add');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
    }

    // Removes a status from DB
    public function status_delete($status) {
      $ch = $this->api_connect($this->url . 'status/');
      $data = array(
        'id_status' => $status
      );
      $data = json_encode($data);

      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    /* User related functions */
    // Gives the DB line of specified user
    public function user_get($mail) {
      $request = 'user/get/';

      $ch = $this->api_connect($this->url . $request);
      // We encode the body to send the email
      $data = array(
        'email' => $mail
      );
      $data = json_encode($data);

      // We use a POST request for security reason
      $ch = $this->api_options($ch, "POST", $data);
      $data = curl_exec($ch);

      $data = json_decode($data, true);
      return $data['response'][0];
    }

    // Adds a new user
    public function user_add($user) {
      $user = json_encode($user);

      $ch = $this->api_connect($this->url . 'user/add/');
      $ch = $this->api_options($ch, "POST", $user);
      $result = curl_exec($ch);
    }

    // Deletes a specified user
    public function user_delete($mail) {
      $ch = $this->api_connect($this->url . 'user/');
      $data = array(
        'email' => $mail
      );
      $data = json_encode($data);

      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    // Changes the password of user
    public function user_change_password($mail, $newpassword) {
      $data = array(
        'email' => $mail,
        'password' => $newpassword
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'user/password/');
      $ch = $this->api_options($ch, "PATCH", $data);
      $result = curl_exec($ch);
      curl_close($ch);
    }

    /* Personal cars related functions */

    // Gets all the personal cars of the specified
    public function personal_car_get_all($user) {
      $user = json_encode($user);

      $ch = $this->api_connect($this->url . "personal_car/get_all");
      $ch = $this->api_options($ch, "POST", $user);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    // Gets a specific personal car of the specified user
    public function personal_car_get($user, $name) {
      $data = array(
        'name' => $name,
        'id_user' => $user
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "personal_car/get");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'][0];
    }

    // Adds a new personal car
    public function personal_car_add($car) {
      $car = json_encode($car);

      $ch = $this->api_connect($this->url . 'personal_car/add/');
      $ch = $this->api_options($ch, "POST", $car);
      $result = curl_exec($ch);
    }

    // Deletes a specific personal car of the specified user
    public function personal_car_delete($user, $name) {
      $car = $this->personal_car_get($user, $name)['id_personal_car'];
      $data = array(
        'id_personal_car' => $car
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'personal_car');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    /* The functions that are facility related */

    // Returns all the facilities
    public function facility_get_all() {
      $ch = $this->api_connect($this->url . 'facility/get_all/');
      $ch = $this->api_options($ch, "POST", NULL);
      $result = curl_exec($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    // Returns the specified facility
    public function facility_get($name) {
      $data = array(
        'name' => $name
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'facility/get/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);

      $result = json_decode($result, true);
      return $result['response'][0];
    }

    // Adds a new facility
    public function facility_add($facility) {
      $facility = json_encode($facility);

      $ch = $this->api_connect($this->url . "facility/add/");
      $ch = $this->api_options($ch, "POST", $facility);
      $response = curl_exec($ch);
    }

    // Deletes a facility
    public function facility_delete($name) {
      $data = array(
        'name' => $name
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'facility/');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }
  }
?>
