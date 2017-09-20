<?php

namespace UPS;

class Common {

  /**
   * [mergeNested description]
   * @param  [type] $default  [description]
   * @param  [type] $override [description]
   * @return [type]           [description]
   */
  static public function mergeNested( $default, $override ) {
    foreach( array_keys($default) AS $key ){
      if (!array_key_exists($key, $override))
        continue;
      if (is_array($default[$key]) && is_array($override[$key]))
        $default[$key] = self::mergeNested( $default[$key], $override[$key] );
      elseif (is_null($override[$key]))
        unset($default[$key]);
      else
        $default[$key] = $override[$key];
    }
    return $default;
  }

}