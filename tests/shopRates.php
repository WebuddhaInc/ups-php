<html>
<body>
<?php

// // Require the main ups class and upsRate
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
  $upsRate = new \UPS\Rate( $upsConnect );

  $res = $upsRate->getRates(array(
    'Request' => array(
      'RequestOption' => 'Shop'
      ),
    'Shipment' => array(
      'ServiceCode' => '03',
      'Description' => 'My Shipment'
      ),
    'Shipper' => array(
      'Name'              => 'Webuddha, Inc.',
      'AttentionName'     => 'John Smith',
      'PhoneNumber'       => '404-555-1212',
      'ShipperNumber'     => '123456',
      'AddressLine1'      => '14 Main St.',
      'AddressLine2'      => '',
      'AddressLine3'      => '',
      'City'              => 'Beverly Hills',
      'StateProvinceCode' => 'CA',
      'PostalCode'        => '90210',
      'CountryCode'       => 'US'
      ),
    'ShipTo' => array(
      'CompanyName'       => 'Webuddha, Inc.',
      'AttentionName'     => 'John Smith',
      'PhoneNumber'       => '404-555-1212',
      'AddressLine1'      => '12 Hollywood Blvd',
      'AddressLine2'      => '',
      'AddressLine3'      => '',
      'City'              => 'Beverly Hills',
      'StateProvinceCode' => 'CA',
      'PostalCode'        => '90210',
      'CountryCode'       => 'US'
      ),
    'Packages' => array(
      array(
        'Description'          => 'Item Name',
        'Weight'               => '5',
        'Length'               => '5',
        'Width'                => '5',
        'Height'               => '5'
        ),
      array(
        'Description'          => 'Item Name',
        'Weight'               => '10',
        'Length'               => '5',
        'Width'                => '5',
        'Height'               => '5'
        )
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
