<html>
<body>
<?php

// Require the main ups class and upsRate
require('../autoload.php');

// Get credentials from a form
$accessNumber = $_POST['accessNumber'];
$username     = $_POST['username'];
$password     = $_POST['password'];

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
  $upsValidateStreet = new \UPS\ValidateStreet( $upsConnect );

  /**
   * Process Request
   * @var [type]
   */
  $res = $upsValidateStreet->validateAddress(array(
    'ConsigneeName'      => '',
    'BuildingName'       => '',
    'AddressLine1'       => 'AIR ROAD SUITE 7',
    'AddressLine2'       => '',
    'AddressLine3'       => '',
    'PoliticalDivision2' => 'SAN DIEGO',
    'PoliticalDivision1' => 'CA',
    'PostcodePrimaryLow' => '92154',
    'CountryCode'        => 'US'
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
