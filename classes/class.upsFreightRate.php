<?php

namespace UPS;

class FreightRate extends Request {

  function __construct($Connector) {
    // Must pass the UPS object to this class for it to work
    $this->connector = $Connector;
  }

  /**
   * [getRates description]
   * @param  [type] $data [description]
   * @return [type]       [description]
   */
  function getFreightRate( $data ) {

    /**
     * Default
     */
    $request = array(
      'Request' => array(
        'RequestOption' => 'RateChecking Option',
        ),
      'ShipFrom' => array(
        'Name' => '',
        'Address' => array(
          'AddressLine' => '',
          'City' => '',
          'StateProvinceCode' => '',
          'PostalCode' => '',
          'CountryCode' => '',
          )
        ),
      'ShipTo' => array(
        'Name' => '',
        'Address' => array(
          'AddressLine' => '',
          'City' => '',
          'StateProvinceCode' => '',
          'PostalCode' => '',
          'CountryCode' => '',
          )
        ),
      'PaymentInformation' => array(
        'ShipmentBillingOption' => array(
          'Code' => '10',
          'Description' => 'PREPAID'
          ),
        'Payer' => array(
          'Name' => '',
          'Address' => array(
            'AddressLine' => '',
            'City' => '',
            'StateProvinceCode' => '',
            'PostalCode' => '',
            'CountryCode' => '',
            )
          )
        ),
      'Service' => array(
        'Code' => '308',
        'Description' => 'UPS Freight LTL',
        ),
      'HandlingUnitOne' => array(
        'Quantity' => 20,
        'Type' => array(
          'Code' => 'PLT',
          )
        ),
      'Commodity' => array(
        'CommodityID' => '',
        'Description' => 'No Description',
        'Weight' => array(
          'UnitOfMeasurement' => array(
            'Code' => 'LBS',
            'Description' => 'Pounds'
            ),
          'Value' => 0,
          ),
        'NumberOfPieces' => 0,
        'Dimensions' => array(
          'UnitOfMeasurement' => array(
            'Code' => 'IN',
            'Description' => 'Inches'
          ),
          'Length' => 0,
          'Width' => 0,
          'Height' => 0,
          ),
        'PackagingType' => array(
          'Code' => 'BAG',
          'Description' => 'BAG'
          ),
        'DangerousGoodsIndicator' => '',
        'FreightClass' => 60,
        'NMFCCommodityCode' => '',
        'CommodityValue' => array(
          'CurrencyCode' => 'USD',
          'MonetaryValue' => 0,
          ),
        ),
      'ShipmentServiceOptions' => array(
        'PickupOptions' => array(
          'HolidayPickupIndicator' => null,
          'InsidePickupIndicator' => null,
          'ResidentialPickupIndicator' => null,
          'WeekendPickupIndicator' => null,
          'LiftGateRequiredIndicator' => null,
          )
        )
      );

    /**
     * Merge Data
     */
    $request = Common::mergeNested( $request, $data );

    /**
     * Process Request
     */
    $response = $this->connector->requestSoapEndpoint('FreightRate', $request);

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
