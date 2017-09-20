<?php

namespace UPS;

class ConnectorCache {

  public function __construct(){
  }

  public function getCache( $endpoint, $request ){
    return false;
  }

  public function setCache( $endpoint, $request, $result ){
    return false;
  }

}