<?php

  namespace App\Controller;

  interface api_interface
  {
    // The functions used to set up the connection
    public function api_connect($url);
    public function api_options($connection, $type, $data);
    public function api_close($connection);

    // The functions we will use to touch to DB
    public function table_get_all($table);
    public function table_get($table, $body);
    public function table_add($table, $body);
    public function table_delete($table, $body);
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

    public function table_get_all($table) {
      $query = $table . "/get_all/";
      $ch = $this->api_connect($this->url . $query);
      $ch = $this->api_options($ch, "POST", []);
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function table_get($table, $body) {
      $query = $table . "/get/";
      $ch = $this->api_connect($this->url . $query);
      $ch = $this->api_options($ch, "POST", json_encode($body));
      $result = curl_exec($ch);
      curl_close($ch);

      $result = json_decode($result, true);
      return $result['response'];
    }

    public function table_add($table, $body) {
      $query = $table . "/add/";
      $ch = $this->api_connect($this->url . $query);
      $ch = $this->api_options($ch, "POST", json_encode($body));
      $result = curl_exec($ch);

      $result = json_decode($result, true);
      return $result['response']['insertId'];
    }

    public function table_delete($table, $body) {
      $query = $table . "/";

      $ch = $this->api_connect($this->url . $query);
      $ch = $this->api_options($ch, "DELETE", json_encode($body));
      $result = curl_exec($ch);
    }
  }
?>
