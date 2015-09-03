<?php

namespace UPS;

class ValidateStreet extends Request {
	var $responseXML;
	var $xmlSent;

	function __construct($Connector) {
		$this->connector = $Connector;
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
			), $data));

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
