<?php

namespace UPS;

class Request {

  var $error = false;
  var $message = null;

  function isError(){
    return $this->error;
  }

  function getMessage(){
    return $this->message;
  }

}