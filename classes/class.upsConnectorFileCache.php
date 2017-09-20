<?php

namespace UPS;

class ConnectorFileCache extends ConnectorCache {

  protected $path;
  protected $timeout;

  public function __construct( $path, $timeout = 86400 ){
    if (is_dir($path) && is_writable($path)) {
      $this->path = preg_replace('/\/+$/', '/', $path . '/');
    }
    $this->timeout = $timeout;
  }

  public function getCache( $endpoint, $request ){
    if ($this->path) {
      $file = md5(serialize($request)) . '.cache';
      $path = $this->path . $file;
      if (file_exists($path)) {
        if (filemtime($path) > time() - $this->timeout) {
          if ($content = @file_get_contents($path)) {
            list($tmp, $response) = unserialize($content);
            return $response;
          }
        }
        @unlink($path);
      }
    }
    return false;
  }

  public function setCache( $endpoint, $request, $result ){
    if ($this->path) {
      $file = md5(serialize($request)) . '.cache';
      $path = $this->path . $file;
      if (file_exists($path)) {
        @unlink($path);
      }
      if (@file_put_contents($path, serialize([$request, $result]))) {
        return true;
      }
    }
    return false;
  }

}