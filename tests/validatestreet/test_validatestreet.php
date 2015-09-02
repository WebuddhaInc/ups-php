<html>
<body>
<?php

// Require the main ups class and upsRate
require('../../autoload.php');

// Get credentials from a form
$accessNumber = $_POST['accessNumber'];
$username     = $_POST['username'];
$password     = $_POST['password'];

// If the form is filled out go get a rate from UPS
if ($accessNumber != '' && $username != '' && $password != '') {

  /**
   * Initialize Connector
   * @var [type]
   */
  $upsConnect = new \UPS\Connector($accessNumber, $username, $password);
  $upsConnect->setTestingMode( false );

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

    /*

    Test Environment only works with some states

    'AddressLine1'       => 'AIR ROAD SUITE 7',
    'AddressLine2'       => '',
    'AddressLine3'       => '',
    'PoliticalDivision2' => 'SAN DIEGO',
    'PoliticalDivision1' => 'CA',
    'PostcodePrimaryLow' => '92154',
    'CountryCode'        => 'US'

    */

    'AddressLine1'       => '3900 Menlo Dr.',
    'AddressLine2'       => '',
    'AddressLine3'       => '',
    'PoliticalDivision2' => 'Atlanta',
    'PoliticalDivision1' => 'GA',
    'PostcodePrimaryLow' => '30340',
    'CountryCode'        => 'US'

    ));

}

?>
<h2>XML Sent to UPS</h2>
<pre><?php echo htmlspecialchars($upsVoid->xmlSent); ?></pre>

<form action="" method="POST">
  Access Key: <input type="text" name="accessNumber" value="<?php echo $accessNumber; ?>" /><br />
  Username: <input type="text" name="username" value="<?php echo $username; ?>" /><br />
  Password: <input type="password" name="password" value="<?php echo $password; ?>" /><br />
  <input type="submit" name="submit" /><br />
</form>

<pre><?php

  print_r(array(
    $res->isError(),
    $res->getMessage(),
    $res
    ));

?></pre>

</body>
</html>
