<html>
<body>
<?php

// Require the main ups class and upsRate
require('../autoload.php');

// Get credentials from a form
$accessNumber = $_POST['accessNumber'];
$username = $_POST['username'];
$password = $_POST['password'];

// If the form is filled out go get a rate from UPS
if( $accessNumber ){

  /**
   * Initialize Connector
   * @var [type]
   */
  $upsConnect = new \UPS\Connector( $accessNumber, $username, $password );
  $upsConnect->setTestingMode( true );

  /**
   * Initialize Request Class
   * @var [type]
   */
  $upsRate = new \UPS\FreightRate( $upsConnect );
  $res = $upsRate->getFreightRate(array(
    'Request' => array(
      'RequestOption' => 'RateChecking Option'
      ),
    'Shipment' => array(
      'ServiceCode' => '02',
      'Description' => 'My Shipment'
      ),
    'ShipFrom' => array(
      'Name'    => 'Webuddha, Inc.',
      'Address' => array(
        'AddressLine'       => '14 Main St.',
        'City'              => 'Beverly Hills',
        'StateProvinceCode' => 'CA',
        'PostalCode'        => '90210',
        'CountryCode'       => 'US'
        )
      ),
    'ShipTo' => array(
      'Name'    => 'Webuddha, Inc.',
      'Address' => array(
        'AddressLine'       => 'Knickerbocker Ave',
        'City'              => 'Brooklyn',
        'StateProvinceCode' => 'NY',
        'PostalCode'        => '11237',
        'CountryCode'       => 'US'
        )
      ),
    'PaymentInformation' => array(
      'Payer' => array(
        'Name'    => 'Webuddha, Inc.',
        'Address' => array(
          'AddressLine'       => 'Knickerbocker Ave',
          'City'              => 'Brooklyn',
          'StateProvinceCode' => 'NY',
          'PostalCode'        => '11237',
          'CountryCode'       => 'US'
          )
        )
      ),
    'Commodity' => array(
      'CommodityValue' => array(
        'MonetaryValue' => 1000
        ),
      'Weight' => array(
        'Value' => 80
      ),
      'Dimensions' => array(
        'Length' => 25,
        'Width' => 50,
        'Height' => 75,
        ),
      'NumberOfPieces' => 1,
      'PackagingType' => array(
        'Code' => 14,
        'Description' => 'CRATE'
        ),
      'FreightClass' => 125,
      )
    ));

}

?>

<form action="" method="POST">
  Access Key: <input type="text" name="accessNumber" value="<?php echo $accessNumber; ?>" /><br />
  Username: <input type="text" name="username" value="<?php echo $username; ?>" /><br />
  Password: <input type="password" name="password" value="<?php echo $password; ?>" /><br />
  <input type="submit" name="submit" /><br />
</form>

<pre><?php

  if( $res )
    print_r(array(
      'isError',
      $res->isError(),
      'getMessage',
      $res->getMessage(),
      'Response',
      $res->get()
      ));

?></pre>

</body>
</html>
