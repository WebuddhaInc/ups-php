<?php

function ups_spl_autoload( $class ){
  if( strpos($class, 'UPS\\') === 0 ){
    $className = substr($class, 4);
    if( !is_file(__DIR__.'/classes/class.ups' . $className . '.php') ){
      throw new Exception("Error Loading $class", 1);
    }
    require_once(__DIR__.'/classes/class.ups' . $className . '.php');
  }
}

spl_autoload_register( 'ups_spl_autoload' );