<?php

namespace UPS;

class Pickup {

  private $error;
  private $message;
  private $connector;
  private $dataSent;
  private $dataResponse;

  function __construct($Connector) {
    // Must pass the UPS object to this class for it to work
    $this->connector = $Connector;
  }

  function processPickupCreation( $data = array() ) {

    /**
     * Merge Data
     */
    $this->dataSent = Common::mergeNested(array(
      'Request' => Array(
        'RequestOption' => 1
        ),
      'RatePickupIndicator' => 'N',
      'Shipper' => Array(
        'Account' => Array(
          'AccountNumber' => '',
          'AccountCountryCode' => ''
          ),
        ),
      'PickupDateInfo' => Array(
        'CloseTime'  => null, // 1400,
        'ReadyTime'  => null, // 0500,
        'PickupDate' => null, // 20100104
        ),
      'PickupAddress' => Array(
        'CompanyName'          => null,
        'ContactName'          => null,
        'AddressLine'          => null,
        'City'                 => null,
        'StateProvince'        => null,
        'PostalCode'           => null,
        'CountryCode'          => null,
        'ResidentialIndicator' => null,
        'Room'                 => null,
        'Floor'                => null,
        'Urbanization'         => null,
        'PickupPoint'          => null,
        'Phone'                => Array(
          'Number'    => null,
          'Extension' => null
          )
        ),
      'AlternateAddressIndicator' => 'Y',
      'PickupPiece' => Array(
        'ServiceCode'            => null,
        'Quantity'               => null,
        'DestinationCountryCode' => 'US',
        'ContainerCode'          => null
        ),
      'TotalWeight' => Array(
        'Weight'            => null,
        'UnitOfMeasurement' => 'LBS'
        ),
      'OverweightIndicator' => 'N',
      'PaymentMethod'       => '01',
      'SpecialInstruction'  => '',
      'ReferenceNumber'     => '',
      'Notification' => Array(
        'ConfirmationEmailAddress' => Array(
          ),
        ),
      ), $data);

    /**
     * Process Request
     */
    $this->dataResponse = $this->connector->requestSoapEndpoint('Pickup', $this->dataSent, 'ProcessPickupCreation');

    /**
     * Catch Error
     */
    if( $this->dataResponse && @$this->dataResponse->Response->Error ){
      $this->error = true;
      $this->message = $this->dataResponse->Response->Error->ErrorCode .': '. $this->dataResponse->Response->Error->ErrorDescription;
    }

    return $this->dataResponse;
  }

}
