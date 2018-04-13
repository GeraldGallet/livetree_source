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
    public function user_get_by_id($id_user);
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
    public function facility_get_by_id($id_facility);
    public function facility_add($facility);
    public function facility_delete($name);

    // Places functions
    public function place_get_all();
    public function place_get($id_facility);
    public function place_get_by_id($id_place);
    public function place_add($place);
    public function place_delete($id_place);

    // Personal cars functions
    public function company_car_get_all($id_facility);
    public function company_car_get($id_facility, $name);
    public function company_car_get_by_id($id_company_car);
    public function company_car_add($car);
    public function company_car_delete($id_company_car);

    // Bornes functions
    public function borne_get_all($id_place);
    public function borne_get($id_borne);
    public function borne_add($borne);
    public function borne_delete($id_borne);

    // Work functions
    public function work_get_all();
    public function work_get($id_user);
    public function work_add($id_user, $id_facility);
    public function work_delete($id_user, $id_facility);

    // Domain functions
    public function domain_get_all();
    public function domain_get($domain);
    public function domain_add($domain);
    public function domain_delete($domain);

    // Domain-Facilities functions
    public function has_domain_get_all();
    public function has_domain_get($id_domain);
    public function has_domain_add($id_facility, $id_domain);
    public function has_domain_delete($id_facility, $id_domain);

    // Phone indicative functions
    public function phone_indicative_get_all();
    public function phone_indicative_get($indicative);
    public function phone_indicative_add($indicative, $country);
    public function phone_indicative_delete($indicative);

    // Accesses functions
    public function has_access_get_all();
    public function has_access_get($id_user);
    public function has_access_add($id_user, $id_place);
    public function has_access_delete($id_user, $id_place);

    // Resa bornes functions
    public function reservation_borne_get_all();
    public function reservation_borne_get($id_place, $date_resa);
    public function reservation_borne_get_by_id($id_resa);
    public function reservation_borne_get_by_place($id_place);
    public function reservation_borne_add($date_resa, $start_time, $end_time, $charge, $id_user, $id_place);
    public function reservation_borne_delete($id_resa);

    // Reasons functions
    public function reason_get_all();
    public function reason_get($id_reason);
    public function reason_add($id_reason, $infos);
    public function reason_delete($id_reason);

    // Resa bornes functions
    public function reservation_car_get_all();
    public function reservation_car_get_by_id($id_resa);
    public function reservation_car_get_by_user($id_user);
    public function reservation_car_add($resa_car);
    public function reservation_car_delete($id_resa);
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

    public function user_get_by_id($id_user) {
      $request = 'user/get_by_id/';

      $ch = $this->api_connect($this->url . $request);
      // We encode the body to send the email
      $data = array(
        'id_user' => $id_user
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

    // Gets all the personal cars of the specified user
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

    public function facility_get_by_id($id_facility) {
      $data = array(
        'id_facility' => $id_facility
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'facility/get_by_id/');
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


    /* The functions that are place-related */

    // Returns all the places
    public function place_get_all() {
      $ch = $this->api_connect($this->url . 'place/get_all/');
      $ch = $this->api_options($ch, "POST", NULL);
      $result = curl_exec($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    // Returns a specific place
    public function place_get($id_facility) {
      $data = array(
        'id_facility' => $id_facility
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'place/get/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    // Gets a place by its id
    public function place_get_by_id($id_place) {
      $data = array(
        'id_place' => $id_place
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'place/get_by_id/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);

      $result = json_decode($result, true);
      return $result['response'][0];
    }

    // Adds a new place to the DB
    public function place_add($place) {
      $place = json_encode($place);

      $ch = $this->api_connect($this->url . "place/add/");
      $ch = $this->api_options($ch, "POST", $place);
      $response = curl_exec($ch);
    }

    // Removes a place from the DB
    public function place_delete($id_place) {
      $data = array(
        'id_place' => $id_place
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'place/');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    /* All the company car related functions */
    // Gets all the company cars of the specified facility
    public function company_car_get_all($id_facility) {
      $fac = array(
        'id_facility' => $id_facility
      );
      $fac = json_encode($fac);

      $ch = $this->api_connect($this->url . "company_car/get_all/");
      $ch = $this->api_options($ch, "POST", $fac);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    // Gets a specific personal car of the specified user
    public function company_car_get($id_facility, $name) {
      $data = array(
        'name' => $name,
        'id_facility' => $id_facility
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "company_car/get/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'][0];
    }

    // Gets a specific personal car of the specified user
    public function company_car_get_by_id($id_company_car) {
      $data = array(
        'id_company_car' => $id_company_car
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "company_car/get_by_id/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'][0];
    }

    // Adds a new personal car
    public function company_car_add($car) {
      $car = json_encode($car);

      $ch = $this->api_connect($this->url . 'company_car/add/');
      $ch = $this->api_options($ch, "POST", $car);
      $result = curl_exec($ch);
    }

    // Deletes a specific personal car of the specified user
    public function company_car_delete($id_company_car) {
      $data = array(
        'id_company_car' => $id_company_car
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'company_car');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    /* Bornes */
    public function borne_get_all($id_place) {
      $fac = array(
        'id_place' => $id_place
      );
      $fac = json_encode($fac);

      $ch = $this->api_connect($this->url . "borne/get_all/");
      $ch = $this->api_options($ch, "POST", $fac);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function borne_get($id_borne) {
      $data = array(
        'id_borne' => $id_borne
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "borne/get/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'][0];
    }

    public function borne_add($borne) {
      $borne = json_encode($borne);

      $ch = $this->api_connect($this->url . 'borne/add/');
      $ch = $this->api_options($ch, "POST", $borne);
      $result = curl_exec($ch);
    }

    public function borne_delete($id_borne) {
      $data = array(
        'id_borne' => $id_borne
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'borne');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    /* Work related functions */

    // Gets all works
    public function work_get_all() {
      $ch = $this->api_connect($this->url . "work/get_all/");
      $ch = $this->api_options($ch, "POST", []);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function work_get($id_user) {
      $data = array(
        'id_user' => $id_user
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "work/get/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function work_add($id_user, $id_facility) {
      $work = array(
        'id_user' => $id_user,
        'id_facility' => $id_facility
      );
      $work = json_encode($work);

      $ch = $this->api_connect($this->url . 'work/add/');
      $ch = $this->api_options($ch, "POST", $work);
      $result = curl_exec($ch);
    }

    public function work_delete($id_user, $id_facility) {
      $data = array(
        'id_user' => $id_user,
        'id_facility' => $id_facility
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'work');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    /* Functions that are domain-related */

    // Gets all lines from domain table
    public function domain_get_all() {
      $ch = $this->api_connect($this->url . "domain/get_all/");
      $ch = $this->api_options($ch, "POST", []);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function domain_get($domain) {
      $data = array(
        'domain' => $domain
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "domain/get/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function domain_add($domain) {
      $data = array(
        'domain' => $domain
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'domain/add/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
    }

    public function domain_delete($domain) {
      $data = array(
        'domain' => $domain
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'domain');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    // Domain-Facilities functions
    public function has_domain_get_all() {
      $ch = $this->api_connect($this->url . "has_domain/get_all/");
      $ch = $this->api_options($ch, "POST", []);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function has_domain_get($id_domain) {
      $data = array(
        'id_domain' => $id_domain
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "has_domain/get/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function has_domain_add($id_facility, $id_domain) {
      $data = array(
        'id_domain' => $id_domain,
        'id_facility' => $id_facility
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'has_domain/add/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
    }

    public function has_domain_delete($id_facility, $id_domain) {
      $data = array(
        'id_domain' => $id_domain,
        'id_facility' => $id_facility
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'has_domain');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    public function phone_indicative_get_all() {
      $ch = $this->api_connect($this->url . "phone_indicative/get_all/");
      $ch = $this->api_options($ch, "POST", []);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function phone_indicative_get($indicative) {
      $data = array(
        'indicative' => $indicative
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "phone_indicative/get/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function phone_indicative_add($indicative, $country) {
      $data = array(
        'indicative' => $indicative,
        'country' => $country
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'phone_indicative/add/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
    }

    public function phone_indicative_delete($indicative) {
      $data = array(
        'indicative' => $indicative
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'phone_indicative');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    // Accesses functions
    public function has_access_get_all() {
      $ch = $this->api_connect($this->url . "has_access/get_all/");
      $ch = $this->api_options($ch, "POST", []);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function has_access_get($id_user) {
      $data = array(
        'id_user' => $id_user
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "has_access/get/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function has_access_add($id_user, $id_place) {
      $data = array(
        'id_user' => $id_user,
        'id_place' => $id_place
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'has_access/add/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
    }

    public function has_access_delete($id_user, $id_place) {
      $data = array(
        'id_user' => $id_user,
        'id_place' => $id_place
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'has_access');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    public function reservation_borne_get_all() {
      $ch = $this->api_connect($this->url . "reservation_borne/get_all/");
      $ch = $this->api_options($ch, "POST", []);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function reservation_borne_get($id_place, $date_resa) {
      $data = array(
        'id_place' => $id_place,
        'date_resa' => $date_resa
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "reservation_borne/get/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function reservation_borne_get_by_place($id_place) {
      $data = array(
        'id_place' => $id_place
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "reservation_borne/get_by_place/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function reservation_borne_get_by_id($id_resa) {
      $data = array(
        'id_resa' => $id_resa
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "reservation_borne/get_by_id/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'][0];
    }


    public function reservation_borne_add($date_resa, $start_time, $end_time, $charge, $id_user, $id_place) {
      $data = array(
        'date_resa' => $date_resa,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'charge' => $charge,
        'id_user' => $id_user,
        'id_place' => $id_place
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'reservation_borne/add/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
    }

    public function reservation_borne_delete($id_resa) {
      $data = array(
        'id_resa' => $id_resa
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'reservation_borne');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    public function reason_get_all() {
      $ch = $this->api_connect($this->url . "reason/get_all/");
      $ch = $this->api_options($ch, "POST", []);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function reason_get($id_reason) {
      $data = array(
        'id_reason' => $id_reason
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "reason/get/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'][0];
    }

    public function reason_add($id_reason, $infos) {
      $data = array(
        'id_reason' => $id_reason,
        'infos' => $infos
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'reason/add/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
    }

    public function reason_delete($id_reason) {
      $data = array(
        'id_reason' => $id_reason
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'reason');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }

    // Resa bornes functions
    public function reservation_car_get_all() {
      $ch = $this->api_connect($this->url . "reservation_car/get_all/");
      $ch = $this->api_options($ch, "POST", []);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function reservation_car_get_by_id($id_resa) {
      $data = array(
        'id_resa' => $id_resa
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "reservation_car/get_by_id/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'][0];
    }

    public function reservation_car_get_by_user($id_user) {
      $data = array(
        'id_user' => $id_user
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . "reservation_car/get_by_user/");
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function reservation_car_add($resa_car) {
      $data = json_encode($resa_car);

      $ch = $this->api_connect($this->url . 'reservation_car/add/');
      $ch = $this->api_options($ch, "POST", $data);
      $result = curl_exec($ch);
    }

    public function reservation_car_delete($id_resa) {
      $data = array(
        'id_resa' => $id_resa
      );
      $data = json_encode($data);

      $ch = $this->api_connect($this->url . 'resa_car/');
      $ch = $this->api_options($ch, "DELETE", $data);
      $result = curl_exec($ch);
    }
  }
?>
