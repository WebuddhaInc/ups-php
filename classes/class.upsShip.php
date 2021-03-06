<?php

namespace UPS;

class Ship {

  private $error;
  private $message;
	private $connector;
	private $xmlSent;
	private $xmlResponse;
	private $dataSent;
	private $dataResponse;

	function __construct($Connector) {
		// Must pass the UPS object to this class for it to work
		$this->connector = $Connector;
	}

	/**
	 * [getShipmentConfirmation description]
	 * @param  array  $request [description]
	 * @return [type]          [description]
	 */
	function getShipmentConfirmation( $data = array() ) {

    /**
     * Merge Data
     */
    $this->dataSent = Common::mergeNested(array(
      'ShipmentConfirmRequest' => array(
        'Request' => array(
          'TransactionReference' => array(
            'CustomerContext' => 'guidlikesubstance',
            'XpciVersion' => '1.0001'
            ),
          'RequestAction' => 'ShipConfirm',
          'RequestOption' => 'nonvalidate',
          ),
        'Shipment' => array(
          'Shipper' => array(
            'Name'          => '',
            'AttentionName' => '',
            'ShipperNumber' => '',
            'Address' => array(
              'AddressLine1'      => '',
              'City'              => '',
              'StateProvinceCode' => '',
              'PostalCode'        => '',
              ),
            ),
          'ShipTo' => array(
            'CompanyName'   => '',
            'AttentionName' => '',
            'PhoneNumber' => array(
              'StructuredPhoneNumber' => array(
                'PhoneDialPlanNumber' => '',
                'PhoneLineNumber'     => '',
                'PhoneExtension'      => '',
                ),
              ),
            'Address' => array(
              'AddressLine1'       => '',
              'City'               => '',
              'StateProvinceCode'  => '',
              'CountryCode'        => '',
              'PostalCode'         => '',
              'ResidentialAddress' => '',
              ),
            ),
          'Service' => array(
            'Code'        => '03',
            'Description' => 'UPS Ground'
            ),
          'PaymentInformation' => array(
            'Prepaid' => array(
              'BillShipper' => array(
                'AccountNumber' => '',
                'CreditCard' => array(
                  'Type'           => '06',
                  'Number'         => '',
                  'ExpirationDate' => '',
                  ),
                ),
              ),
            'BillThirdParty' => array(
							'BillThirdPartyShipper' => array(
								'AccountNumber' => '',
								'ThirdParty' => array(
									'Address' => array(
										'PostalCode' => '',
										'CountryCode' => '',
										),
									),
								),
            	),
          	'FreightCollect' => array(
          		'BillReceiver' => array(
          			'AccountNumber' => '',
          			'Address' => array(
									'PostalCode' => '',
									'CountryCode' => '',
          				),
          			),
          		),
            'ConsigneeBilled' => '',
            ),
          'ShipmentServiceOptions' => array(
            /*
            This is now part of the Pickup API
            'OnCallAir' => array(
              'PickupDetails' => array(
                'PickupDate'        => '',
                'EarliestTimeReady' => '',
                'LatestTimeReady'   => '',
                'ContactInfo' => array(
                  'Name' => '',
                  'PhoneNumber' => '',
                  ),
                ),
              ),
              */
            ),
          'Package' => array(
            'PackagingType' => array(
              'Code' => '02',
              ),
            'Dimensions' => array(
              'UnitOfMeasurement' => array(
                'Code' => 'IN',
                ),
              'Length' => '',
              'Width'  => '',
              'Height' => '',
              ),
            'PackageWeight' => array(
              'Weight' => '',
              ),
            'ReferenceNumber' => array(
              'Code'  => '02',
              'Value' => '1234567',
              ),
            'PackageServiceOptions' => array(
              'InsuredValue' => array(
                'CurrencyCode'  => 'USD',
                'MonetaryValue' => ''
                ),
              'VerbalConfirmation' => array(
                'Name' => '',
                'PhoneNumber' => '',
                ),
              ),
            ),
          ),
        'LabelSpecification' => array(
          'LabelPrintMethod' => array(
            'Code' => 'GIF',
            ),
          'HTTPUserAgent' => 'Mozilla/4.5',
          'LabelImageFormat' => array(
            'Code' => 'GIF',
            ),
          ),
        ),
      ), $data);

    /**
     * Package XML
     */
    $xmlBuilder = new \UPS\XMLBuilder();
    $xml = $this->connector->getAccessRequestXMLString()
         . $xmlBuilder->buildXmlFromArray($this->dataSent);
		$this->xmlSent = $xml;

    /**
     * Process Request
     */
		$this->xmlResponse = $this->connector->sendEndpointXML('ShipConfirm', $xml);
    $this->dataResponse = new \SimpleXMLElement($this->xmlResponse);

    /**
     * Catch Error
     */
    if( $this->dataResponse && @$this->dataResponse->Response->Error ){
      $this->error = true;
      $this->message = $this->dataResponse->Response->Error->ErrorCode .': '. $this->dataResponse->Response->Error->ErrorDescription;
    }

    return $this->dataResponse;
	}

  function getShipmentAcceptance( $data = array() ) {

    /**
     * Merge Data
     */
    $this->dataSent = Common::mergeNested(array(
      'ShipmentAcceptRequest' => array(
        'Request' => array(
          'TransactionReference' => array(
            'XpciVersion' => 'guidlikesubstance',
            'XpciVersion' => '1.0001',
            ),
          'RequestAction' => 'ShipAccept',
          ),
        'ShipmentDigest' => '',
        ),
      ), $data);

    /**
     * Package XML
     */
    $xmlBuilder = new \UPS\XMLBuilder();
    $xml = $this->connector->getAccessRequestXMLString()
         . $xmlBuilder->buildXmlFromArray($this->dataSent);
    $this->xmlSent = $xml;

    /**
     * Process Request
     */
    $this->xmlResponse = $this->connector->sendEndpointXML('ShipAccept', $xml);
    $this->dataResponse = new \SimpleXMLElement($this->xmlResponse);

    /**
     * Catch Error
     */
    if( $this->dataResponse && @$this->dataResponse->Response->Error ){
      $this->error = true;
      $this->message = $this->dataResponse->Response->Error->ErrorCode .': '. $this->dataResponse->Response->Error->ErrorDescription;
    }

    return $this->dataResponse;
  }

	/**
	 * Old Methods
   *
   *
   *
   *
   *

	function buildRequestXML() {
		$xml = $this->connector->getAccessRequestXMLString();
		$ShipmentConfirmRequestXML = new \UPS\XMLBuilder();
		$ShipmentConfirmRequestXML->push('ShipmentConfirmRequest');
			$ShipmentConfirmRequestXML->push('Request');
				$ShipmentConfirmRequestXML->push('TransactionReference');
					$ShipmentConfirmRequestXML->element('CustomerContext', 'guidlikesubstance');
					$ShipmentConfirmRequestXML->element('XpciVersion', '1.0001');
				$ShipmentConfirmRequestXML->pop();
				$ShipmentConfirmRequestXML->element('RequestAction', 'ShipConfirm');
				$ShipmentConfirmRequestXML->element('RequestOption', 'nonvalidate');
				$ShipmentConfirmRequestXML->pop(); // end Request
			$ShipmentConfirmRequestXML->push('Shipment');
				$ShipmentConfirmRequestXML->push('Shipper');
					$ShipmentConfirmRequestXML->element('Name', 'Joes Garage');
					$ShipmentConfirmRequestXML->element('AttentionName', 'Mark Sanborn');
					$ShipmentConfirmRequestXML->element('ShipperNumber', '300YA9');
					$ShipmentConfirmRequestXML->push('Address');
						$ShipmentConfirmRequestXML->element('AddressLine1', '10000 Preston Rd');
						$ShipmentConfirmRequestXML->element('City', 'Whitehall');
						$ShipmentConfirmRequestXML->element('StateProvinceCode', 'MT');
						$ShipmentConfirmRequestXML->element('PostalCode', '59759');
					$ShipmentConfirmRequestXML->pop(); // end Address
				$ShipmentConfirmRequestXML->pop(); // end Shipper
				$ShipmentConfirmRequestXML->push('ShipTo');
					$ShipmentConfirmRequestXML->element('CompanyName', 'Pep Boys');
					$ShipmentConfirmRequestXML->element('AttentionName', 'Manny');
					$ShipmentConfirmRequestXML->push('PhoneNumber');
						$ShipmentConfirmRequestXML->push('StructuredPhoneNumber');
							$ShipmentConfirmRequestXML->element('PhoneDialPlanNumber', '410');
							$ShipmentConfirmRequestXML->element('PhoneLineNumber', '5551212');
							$ShipmentConfirmRequestXML->element('PhoneExtension', '1234');
						$ShipmentConfirmRequestXML->pop(); // end StrurcturedPhoneNumber
					$ShipmentConfirmRequestXML->pop(); // end PhoneNumber
					$ShipmentConfirmRequestXML->push('Address');
						$ShipmentConfirmRequestXML->element('AddressLine1', '201 York Rd');
						$ShipmentConfirmRequestXML->element('City', 'Timonium');
						$ShipmentConfirmRequestXML->element('StateProvinceCode', 'MD');
						$ShipmentConfirmRequestXML->element('CountryCode', 'US');
						$ShipmentConfirmRequestXML->element('PostalCode', '21093');
						$ShipmentConfirmRequestXML->element('ResidentialAddress', '');
					$ShipmentConfirmRequestXML->pop(); // end Address
				$ShipmentConfirmRequestXML->pop(); // end ShipTo
				$ShipmentConfirmRequestXML->push('Service');
					$ShipmentConfirmRequestXML->element('Code', '03');
					$ShipmentConfirmRequestXML->element('Description', 'UPS Ground');
				$ShipmentConfirmRequestXML->pop(); // end Service
				$ShipmentConfirmRequestXML->push('PaymentInformation');
					$ShipmentConfirmRequestXML->push('Prepaid');
						$ShipmentConfirmRequestXML->push('BillShipper');
							$ShipmentConfirmRequestXML->push('CreditCard');
								$ShipmentConfirmRequestXML->element('Type', '06');
								$ShipmentConfirmRequestXML->element('Number', '4111111111111111');
								$ShipmentConfirmRequestXML->element('ExpirationDate', '011909');
							$ShipmentConfirmRequestXML->pop(); // end CreditCard
						$ShipmentConfirmRequestXML->pop(); // end BillShipper
					$ShipmentConfirmRequestXML->pop(); // end Prepaid
				$ShipmentConfirmRequestXML->pop(); // end PaymentInformation
				$ShipmentConfirmRequestXML->push('ShipmentServiceOptions');
					$ShipmentConfirmRequestXML->push('OnCallAir');
						$ShipmentConfirmRequestXML->push('PickupDetails');
							$ShipmentConfirmRequestXML->element('PickupDate', '20090115');
							$ShipmentConfirmRequestXML->element('EarliestTimeReady', '0945');
							$ShipmentConfirmRequestXML->element('LatestTimeReady', '1445');
							$ShipmentConfirmRequestXML->push('ContactInfo');
								$ShipmentConfirmRequestXML->element('Name', 'JaneSmith');
								$ShipmentConfirmRequestXML->element('PhoneNumber', '9725551234');
							$ShipmentConfirmRequestXML->pop(); // end ContactInfo
						$ShipmentConfirmRequestXML->pop(); // end PickupDetails
					$ShipmentConfirmRequestXML->pop(); // end OnCallAir
				$ShipmentConfirmRequestXML->pop(); // end ShipmentServiceOptions
				$ShipmentConfirmRequestXML->push('Package');
					$ShipmentConfirmRequestXML->push('PackagingType');
						$ShipmentConfirmRequestXML->element('Code', '02');
					$ShipmentConfirmRequestXML->pop(); // end PackagingType
					$ShipmentConfirmRequestXML->push('Dimensions');
						$ShipmentConfirmRequestXML->push('UnitOfMeasurement');
							$ShipmentConfirmRequestXML->element('Code', 'IN');
						$ShipmentConfirmRequestXML->pop(); // end UnitOfMeasurement
						$ShipmentConfirmRequestXML->element('Length', '22');
						$ShipmentConfirmRequestXML->element('Width', '20');
						$ShipmentConfirmRequestXML->element('Height', '18');
					$ShipmentConfirmRequestXML->pop(); // end Dimensions
					$ShipmentConfirmRequestXML->push('PackageWeight');
						$ShipmentConfirmRequestXML->element('Weight', '14.1');
					$ShipmentConfirmRequestXML->pop(); // end PackageWeight
					$ShipmentConfirmRequestXML->push('ReferenceNumber');
						$ShipmentConfirmRequestXML->element('Code', '02');
						$ShipmentConfirmRequestXML->element('Value', '1234567');
					$ShipmentConfirmRequestXML->pop(); // end ReferenceNumber
					$ShipmentConfirmRequestXML->push('PackageServiceOptions');
						$ShipmentConfirmRequestXML->push('InsuredValue');
							$ShipmentConfirmRequestXML->element('CurrencyCode', 'USD');
							$ShipmentConfirmRequestXML->element('MonetaryValue', '149.99');
						$ShipmentConfirmRequestXML->pop(); // End Insured Value
						// $ShipmentConfirmRequestXML->push('VerbalConfirmation');
						// 	$ShipmentConfirmRequestXML->element('Name', 'SidneySmith');
						// 	$ShipmentConfirmRequestXML->element('PhoneNumber', '4105551234');
						// $ShipmentConfirmRequestXML->pop(); // end VerbalConfirmation
					$ShipmentConfirmRequestXML->pop(); // end PackageServiceOptions
				$ShipmentConfirmRequestXML->pop(); // end Package
			$ShipmentConfirmRequestXML->pop(); // end Shipment
			$ShipmentConfirmRequestXML->push('LabelSpecification');
				$ShipmentConfirmRequestXML->push('LabelPrintMethod');
					$ShipmentConfirmRequestXML->element('Code', 'GIF');
				$ShipmentConfirmRequestXML->pop(); // end LabelPrintMethod
				$ShipmentConfirmRequestXML->element('HTTPUserAgent', 'Mozilla/4.5');
				$ShipmentConfirmRequestXML->push('LabelImageFormat');
					$ShipmentConfirmRequestXML->element('Code', 'GIF');
				$ShipmentConfirmRequestXML->pop(); // end LabelImageFormat
			$ShipmentConfirmRequestXML->pop(); // end LabelSpecification
		$ShipmentConfirmRequestXML->pop(); // ShipmentConfirmRequest
		$xml .= $ShipmentConfirmRequestXML->getXml();
		$xmlResponse = $this->connector->sendEndpointXML('ShipConfirm', $xml);
		$this->xmlSent = $xml;
		$this->xmlResponse = $xmlResponse;
		return $xmlResponse;
	}

	function buildShipmentAcceptXML($ShipmentDigest) {
		$xml = new \UPS\XMLBuilder();
		$xml->push('ShipmentAcceptRequest');
			$xml->push('Request');
				$xml->push('TransactionReference');
					$xml->element('CustomerContext', 'guidlikesubstance');
					$xml->element('XpciVersion', '1.0001');
				$xml->pop(); // end TransactionReference
			$xml->element('RequestAction', 'ShipAccept');
			$xml->pop(); // end Request
		$xml->element('ShipmentDigest', $ShipmentDigest);
		$xml->pop(); // end ShipmentAcceptRequest
		$ShipmentAcceptXML = $this->connector->getAccessRequestXMLString();
		$ShipmentAcceptXML .= $xml->getXml();
		$xmlResponse = $this->connector->sendEndpointXML('ShipAccept', $ShipmentAcceptXML);
		$this->xmlResponse = $xmlResponse;
		return $ShipmentAcceptXML;
	}

	function responseArray() {
		$xmlParser = new \UPS\XMLParser();
		$responseArray = $xmlParser->xmlParser($this->xmlResponse);
		$responseArray = $xmlParser->getData();
		return $responseArray;
	}

  */

}
