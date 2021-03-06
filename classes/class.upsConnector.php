<?php

namespace UPS;

class Connector {

  var $License;
  var $User;
  var $Pass;

  var $templatePath;
  var $debugMode;
  var $accessRequest;
  var $cacheHandler;

  /**
   * [__construct description]
   * @param [type] $license [description]
   * @param [type] $user    [description]
   * @param [type] $pass    [description]
   */
  function __construct( $license, $user, $pass ){
    $this->License       = $license;
    $this->User          = $user;
    $this->Pass          = $pass;
    $this->accessRequest = false;
    $this->templatePath  = 'xml/'; // No beginning slash if path is relative
  }

  /**
   * [setTemplatePath description]
   * @param [type] $path [description]
   */
  function setTemplatePath($path){
    // TODO: set the default path to ../xml/ incase user doesn't set it
    // Set the template path for xml templates
    if($path !== ''){
      $this->templatePath = $path;
    }
    return true;
  }

  /**
   * [setTestingMode description]
   * @param [type] $bool [description]
   */
  function setTestingMode($bool){
    if($bool == 1){
      $this->debugMode = true;
      $this->connectorUrl = 'https://wwwcie.ups.com'; // Don't put a trailing slash here or world will collide.
      $this->endpointUrl = 'https://wwwcie.ups.com/webservices/';
    }
    else{
      $this->debugMode = false;
      $this->connectorUrl = 'https://www.ups.com';
      $this->endpointUrl = 'https://onlinetools.ups.com/webservices/';
    }
    return true;
  }

  function setCacheHandler( $handler ){
    $this->cacheHandler = $handler;
  }

  /**
   * [getXMLString description]
   * @param  [type] $templateFile [description]
   * @param  array  $data         [description]
   * @return [type]               [description]
   */
  function getXMLString( $templateFile, $data=array() ){
    $buffer = false;
    $path = $templateFile;
    if( !is_file($path) ){
      $path = $this->templatePath . $templateFile;
    }
    if( !is_file($path) ){
      $path = __DIR__ . '/../xml/' . $templateFile;
    }
    if( is_file($path) ){
      $buffer = file_get_contents($path);
      foreach( $data AS $key => $value ){
        if( is_array($value) ){
          if( isset($value['file']) ){
            $value = $this->getXMLString( $value['file'], $value['data'] );
          }
          else if( count($value) && isset($value[0]['file']) ) {
            $valueRes = '';
            foreach( $value AS $valueRow ){
              $valueRes .= $this->getXMLString( $valueRow['file'], $valueRow['data'] );
            }
            $value = $valueRes;
          }
          else {
            $value = 'NA';
          }
        }
        $buffer = str_replace('{'.$key.'}', $value, $buffer);
      }
      $buffer = preg_replace('/\{[A-Za-z0-9\_\.]+\}/','',$buffer);
    }
    else {
      throw new Exception("XML File Not Found: $path", 1);
    }
    return $buffer;
  }

  /**
   * [sandwich description]
   * @param  [type] $templateFile [description]
   * @param  [type] $findArray    [description]
   * @param  [type] $replaceArray [description]
   * @return [type]               [description]
   */
  function sandwich($templateFile, $findArray, $replaceArray){
    $data = array();
    for($i=0;$i<count($findArray);$i++){
      $data[ preg_replace('/^\{(.*)\}$/','$1',$findArray[$i]) ] = isset($replaceArray[$i]) ? $replaceArray[$i] : null;
    }
    return $this->getXMLString( $templateFile, $data );
  }

  /**
   * [access description]
   * @return [type] [description]
   */
  function getAccessRequestXMLString(){
    $this->accessRequest = true;
    return $this->getXMLString('AccessRequest.xml', array(
        'LICENSE'  => $this->License,
        'USER_ID'  => $this->User,
        'PASSWORD' => $this->Pass
        ));
  }

  function requestSoapEndpoint( $endpoint, $request, $function = null ){

    /**
     * Get Cache
     */
    if ($this->cacheHandler) {
      if ($cached = $this->cacheHandler->getCache( $endpoint, $request )) {
        return $cached;
      }
    }

    /**
     * Query
     */
    try {

      /**
       * Stage
       */
      $wsdl_file = dirname(dirname(__FILE__)) .'/wsdl/'. $endpoint .'.wsdl';
      $endpoint_url = $this->endpointUrl . $endpoint;

      /**
       * Initialize
       */
        $client = new \SoapClient($wsdl_file, array(
          'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
          'trace'        => 1,
          'exceptions'   => true,
          ));
        $client->__setLocation($endpoint_url);

      /**
       * Security
       */
        $UPSSecurity = array(
          'UsernameToken' => array(
            'Username' => $this->User,
            'Password' => $this->Pass
          ),
          'ServiceAccessToken' => array(
            'AccessLicenseNumber' => $this->License
          )
        );
        $header = new \SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$UPSSecurity);
        $client->__setSoapHeaders($header);

      /**
       * Call Endpoint
       */
        // $res = $client->__soapCall('ProcessFreightRate', array($request));
        $res = $client->__soapCall( $function ?: 'Process' . $endpoint, array($request));

    } catch (\SoapFault $fault) {

      $res = $fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Code .': '. $fault->detail->Errors->ErrorDetail->PrimaryErrorCode->Description;

    }
    catch(\Exception $e) {

      $res = $e->getMessage();

    }

    /**
     * Parse Response
     */
    $response = new \UPS\SoapResponse( $res );

    /**
     * Cache Response
     */
    if ($res && $this->cacheHandler) {
      $this->cacheHandler->setCache( $endpoint, $request, $response );
    }

    /**
     * Return Response
     */
    return $response;

  }

  /**
   * [requestEndpoint description]
   * @param  [type] $type    [description]
   * @param  [type] $request [description]
   * @param  [type] $data    [description]
   * @return [type]          [description]
   */
  function requestEndpoint( $type, $request, $data ){

    /**
     * Get Cache
     */
    if ($this->cacheHandler) {
      if ($cached = $this->cacheHandler->getCache( $request, $data )) {
        return $cached;
      }
    }

    /**
     * Get XML String
     */
    $xmlString
      = $this->getAccessRequestXMLString()
      . $this->getXMLString( $type .'/'. $request . '.xml', $data );

    /**
     * Process Request
     */
    $res = $this->sendEndpointXML( $type, $xmlString );

    /**
     * Parse Response
     */
    $response = new \UPS\Response( $res );

    /**
     * Cache Response
     */
    if ($res && $this->cacheHandler) {
      $this->cacheHandler->setCache( $endpoint, $request, $response );
    }

    /**
     * Return Response
     */
    return $response;

  }

  /**
   * [sendEndpointXML description]
   * @param  [type] $type [description]
   * @param  [type] $xml  [description]
   * @return [type]       [description]
   */
  function sendEndpointXML( $endpoint, $request ){

    /**
     * Get Cache
     */
    if ($this->cacheHandler) {
      if ($cached = $this->cacheHandler->getCache( $endpoint, $request )) {
        return $cached;
      }
    }

    // This function will return all of the relevant response info in the form of an Array
    if ($this->accessRequest != true) {
      throw new Exception('AccessRequest Not Prepared', 1);
    }
    else {
      $output = $request;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->connectorUrl.'/ups.app/xml/'.$endpoint);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 60);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $output);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      if (false) {
        $fh = fopen(__DIR__ . '/curl.log', 'w');
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_STDERR, $fh);
        $curlReturned = curl_exec($ch);
        fwrite(print_r([$curlReturned, curl_curlinfo($ch)], true), $fh);
        fclose($fh);
      }
      else {
        $curlReturned = curl_exec($ch);
      }
      curl_close($ch);
      // Find out if the UPS service is down
      preg_match_all('/HTTP\/1\.\d\s(\d+)/',$curlReturned,$matches);
      foreach($matches[1] as $key=>$value) {
        if ($value != 100 && $value != 200) {
          throw new Exception("The UPS service seems to be down with HTTP/1.1 $value");
          return false;
        }
        else {

          /**
           * Parse Response
           */
          $response = strstr($curlReturned, '<'); // substr($curlReturned, strpos($curlReturned, "\r\n\r\n")+4); // Seperate the html header and the actual XML because we turned CURLOPT_HEADER to 1

          /**
           * Cache Response
           */
          if ($this->cacheHandler) {
            $this->cacheHandler->setCache( $endpoint, $request, $response );
          }

          /**
           * Return Response
           */
          return $response;
        }
      }
    }
  }

}
