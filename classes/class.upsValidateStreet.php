<?php

namespace UPS;

class ValidateStreet {
	var $responseXML;
	var $xmlSent;

	function __construct($upsObj) {
		// Must pass the UPS object to this class for it to work
		$this->ups = $upsObj;
	}

	function buildRequestXML( $address ) {

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

		$RequestXML = $this->ups->access();
		$RequestXML .= $xml->getXml();

		$responseXML = $this->ups->request('XAV', $RequestXML);
		$this->responseXML = $responseXML;
		$this->xmlSent = $RequestXML;

		return $RequestXML;
	}

	function validateAddress( $address ) {
		$xml = new \UPS\XMLBuilder();
		$xml->push('VoidShipmentRequest');
			$xml->push('Request');
				$xml->element('RequestAction', '1');
			$xml->pop(); // end Request
		$xml->push('ExpandedVoidShipment');
			$xml->element('ShipmentIdentificationNumber', $ShipmentIdentificationNumber);
			foreach ($TrackingNumber as $tracking) {
				$xml->element('TrackingNumber', $tracking);
			}
		$xml->pop(); // end ExpandedVoidShipment
		$xml->pop(); // end VoidShipmentRequest


		$voidMultiShipment = $this->ups->access();
		$voidMultiShipment .= $xml->getXml();

		$responseXML = $this->ups->request('Void', $voidMultiShipment);
		$this->responseXML = $responseXML;
		$this->xmlSent = $voidMultiShipment;

	}
}

?>
