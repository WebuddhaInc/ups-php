<?php

namespace UPS;

class SoapResponse {

  var $response = array();
  var $error    = false;
  var $message  = null;

  function __construct( $response ){
    if (is_object($response)) {
      $this->response = $response;
      if (!isset($response->Response->ResponseStatus) || $response->Response->ResponseStatus->Code != 1) {
        $this->error = true;
        $this->message = $response->Response->Description;
      }
    }
    else {
      $this->error = true;
      $this->message = $response ? (string)$response : "Invalid Response / Error Unknown";
    }
  }

  function get( $key = null, $def = null ){
    $val = $this->response;
    if( $key ){
      $keys = explode('/', $key);
      while( count($keys) ){
        $key = array_shift($keys);
        if( isset($val->{$key}) ){
          $val = $val->{$key};
          continue;
        }
        return $def;
      }
    }
    return $val;
  }

  function isError(){
    return $this->error;
  }

  function getMessage(){
    return $this->message;
  }

}