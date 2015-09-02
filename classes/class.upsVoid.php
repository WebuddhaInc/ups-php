<?php

namespace UPS;

class Void {

	var $responseXML;
	var $xmlSent;

	function __construct($Connector) {
		// Must pass the UPS object to this class for it to work
		$this->connector = $Connector;
	}

	function buildRequestXML($ShipmentIdentificationNumber) {
		$xml = new \UPS\XMLBuilder();
		$xml->push('VoidShipmentRequest');
			$xml->push('Request');
				$xml->element('RequestAction', '1');
			$xml->pop(); // end Request
			$xml->element('ShipmentIdentificationNumber', $ShipmentIdentificationNumber);
		$xml->pop(); // end VoidShipmentRequest

		$VoidRequestXML = $this->connector->getAccessRequestXMLString();
		$VoidRequestXML .= $xml->getXml();

		$responseXML = $this->connector->sendEndpointXML('Void', $VoidRequestXML);
		$this->responseXML = $responseXML;
		$this->xmlSent = $VoidRequestXML;

		return $VoidRequestXML;
	}

	function voidMultiShipment($ShipmentIdentificationNumber,$TrackingNumber) {
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


		$voidMultiShipment = $this->connector->getAccessRequestXMLString();
		$voidMultiShipment .= $xml->getXml();

		$responseXML = $this->connector->sendEndpointXML('Void', $voidMultiShipment);
		$this->responseXML = $responseXML;
		$this->xmlSent = $voidMultiShipment;

	}
}

?>
