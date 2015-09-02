<?php

namespace UPS;

class Exception extends \Exception {

  function __construct( $message = '', $code = 0, $previous = null ){
    echo '<div class="msg error_msg">' . $message . '</div>';
    parent::__construct($message, $code, $previous);
  }

}