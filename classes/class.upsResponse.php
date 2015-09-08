<?php

namespace UPS;

class Response {

  var $error = false;
  var $message = null;

  function __construct( $rawXML ){

    if( $rawXML ){
      $this->xmlParser = new \UPS\XMLParser( $rawXML );
      $xmlRoot = $this->xmlParser->GetRoot();
      $xmlData = $this->xmlParser->GetData();
      if( empty($xmlRoot) || empty($xmlData) || empty($xmlData[$xmlRoot]) ){
        $this->error = true;
        $this->message = 'Cannot Parse Response';
      }
      else if( $this->get($xmlRoot . '/Response/Error') ){
        $this->error = true;
        $this->message = $this->get($xmlRoot . '/Response/Error/ErrorDescription/VALUE');
      }
    }
    else {
      $this->error = true;
      $this->message = "Invalid Response / Error Unknown";
    }

  }

  function get( $key = null, $def = null ){
    $xmlData = $this->xmlParser->GetData();
    if( $key ){
      $keys = explode('/', $key);
      while( count($keys) ){
        $key = array_shift($keys);
        if( isset($xmlData[ $key ]) ){
          $xmlData = $xmlData[ $key ];
          continue;
        }
        return $def;
      }
    }
    return $xmlData;
  }

  function isError(){
    return $this->error;
  }

  function getMessage(){
    return $this->message;
  }

}