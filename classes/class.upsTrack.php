<?php

namespace UPS;

class Track {

	var $xmlSent;
	var $trackResponse;

	function __construct($Connector){
		// Must pass the UPS object to this class for it to work
		$this->connector = $Connector;
	}

	function track($trackingNumber){
		$xml = $this->connector->getAccessRequestXMLString();
		$xml .= $this->connector->getXMLString('Tracking/TrackRequest.xml', array(
				'TRACKING_NUMBER' => $trackingNumber
				));
		$this->xmlSent = $xml;
		$responseXML = $this->connector->sendEndpointXML('Track', $xml);
		$xmlParser = new \UPS\XMLParser();
		$fromUPS = $xmlParser->xmlparser($responseXML);
		$fromUPS = $xmlParser->getData();
		$this->trackResponse = $fromUPS;
		return $fromUPS;
	}

	// Output the entire array of XML returned by UPS
	function returnResponseArray() {
		$trackResponse = $this->trackResponse;
		return $trackResponse;
	}

	function isResponseError() {
		$rateResponse = $this->rateResponse;
		$responseStatusCode = $rateResponse['TrackResponse']['Response']['ResponseStatusCode']['VALUE'];
		if ($responseStatusCode < 1) {
			return true;
		}
		else {
			return false;
		}
	}

}
