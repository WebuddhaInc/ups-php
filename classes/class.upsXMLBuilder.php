<?php

namespace UPS;

// Simon Willison, 16th April 2003
// Based on Lars Marius Garshol's Python XMLWriter class
// See http://www.xml.com/pub/a/2003/04/09/py-xml.html

class XMLBuilder {
  var $xml;
  var $indent;
  var $stack = array();
  function __construct($indent = '  ') {
    $this->indent = $indent;
    $this->xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
  }
  function _indent() {
    for ($i = 0, $j = count($this->stack); $i < $j; $i++) {
      $this->xml .= $this->indent;
    }
  }
  function push($element, $attributes = array()) {
    $this->_indent();
    $this->xml .= '<'.$element;
    foreach ($attributes as $key => $value) {
      $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
    }
    $this->xml .= ">\n";
    $this->stack[] = $element;
  }
  function element($element, $content, $attributes = array()) {
    $this->_indent();
    $this->xml .= '<'.$element;
    foreach ($attributes as $key => $value) {
      $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
    }
    $this->xml .= '>'.htmlentities($content).'</'.$element.'>'."\n";
  }
  function emptyelement($element, $attributes = array()) {
    $this->_indent();
    $this->xml .= '<'.$element;
    foreach ($attributes as $key => $value) {
      $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
    }
    $this->xml .= " />\n";
  }
  function pop() {
    $element = array_pop($this->stack);
    $this->_indent();
    $this->xml .= "</$element>\n";
  }
  function getXml() {
    return $this->xml;
  }

  function buildXmlFromArray( $data ) {
    try {
      $domtree = new \DOMDocument('1.0');
      $this->__buildXmlFromArray( $data, $domtree, $domtree );
      return $domtree->saveHTML();
    } catch (Exception $e) {
      die(__LINE__.': '.__FILE__);
    }
  }
  private function __buildXmlFromArray( $data, &$domtree, &$node ) {
    foreach( $data as $key => $value ) {
      if( is_numeric($key) ){
        $key = 'item'.$key; //dealing with <0/>..<n/> issues
      }
      if( is_array($value) ) {
        $subnode = $domtree->createElement($key);
        $this->__buildXmlFromArray($value, $domtree, $subnode);
        $node->appendChild($subnode);
      }
      else {
        $node->appendChild($domtree->createElement($key, htmlspecialchars($value)));
      }
    }
  }

}
