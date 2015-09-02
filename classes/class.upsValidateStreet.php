<?php

namespace UPS;

class ValidateStreet extends Request {
	var $responseXML;
	var $xmlSent;

	function __construct($Connector) {
		// Must pass the UPS object to this class for it to work
		$this->connector = $Connector;
	}

	function buildRequestXML( $address ) {

 		/**
 		 * Build XML
 		 */
 		$xml = new \UPS\XMLBuilder();
		$xml->push('AddressValidationRequest', array('xml:lang' => 'en-US'));
			$xml->push('Request');
				$xml->element('RequestAction', 'XAV');
				$xml->element('RequestOption', '3');
			$xml->pop(); // end Request
			$xml->element('MinimumListSize', '3');
			$xml->push('AddressKeyFormat');
				$xml->element('ConsigneeName', 'Company Name');
				$xml->element('BuildingName', 'Building Name');
				$xml->element('AddressLine', 'AIR ROAD SUITE 7');
				$xml->element('PoliticalDivision2', 'SAN DIEGO');
				$xml->element('PoliticalDivision1', 'CA');
				$xml->element('PostcodePrimaryLow', '92154');
				$xml->element('CountryCode', 'US');
			$xml->pop(); // end AddressKeyFormat
		$xml->pop(); // end AddressValidationRequest

		/**
		 * Process Request
		 * @var [type]
		 */
		$RequestXML = $this->connector->getAccessRequestXMLString();
		$RequestXML .= $xml->getXml();
		print_r($RequestXML);die();
		$responseXML = $this->connector->sendEndpointXML('XAV', $RequestXML);

		$this->responseXML = $responseXML;
		$this->xmlSent = $RequestXML;
		return $RequestXML;
	}

	function validateAddress( $data ) {

 		/**
 		 * Process Request
 		 */
		$response = $this->connector->requestEndpoint('XAV', 'AddressValidationRequest', array_merge(array(
				'MinimumListSize'    => '3',
				'ConsigneeName'      => '',
				'BuildingName'       => '',
				'AddressLine1'       => '',
				'AddressLine2'       => '',
				'AddressLine3'       => '',
				'PoliticalDivision2' => '',
				'PoliticalDivision1' => '',
				'PostcodePrimaryLow' => '',
				'CountryCode'        => ''
	 			), $data) );

 		/**
 		 * Catch Error
 		 */
 		if( $response->isError() ){
 			$this->error = true;
 			$this->message = $response->getMessage();
 		}

 		/**
 		 * Return
 		 */
		return $response;

	}

}
