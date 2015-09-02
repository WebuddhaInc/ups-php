<html>
<body>
<pre>
<?php

// Require the main ups class and upsRate
require('../../autoload.php');

// Get credentials from a form
$accessNumber = $_POST['accessNumber'];
$username     = $_POST['username'];
$password     = $_POST['password'];

// If the form is filled out go get a rate from UPS
if ($accessNumber != '' && $username != '' && $password != '') {

  //Initiate the main UPS class
  $upsConnect = new \UPS\Connector($accessNumber, $username, $password);
  $upsConnect->setTemplatePath('../../xml/');
  $upsConnect->setTestingMode(1); // Change this to 0 for production

  $upsValidateStreet = new \UPS\ValidateStreet($upsConnect);
  $upsValidateStreet->buildRequestXML(array(
    'AddressLine' => array(),
    'PoliticalDivision2' => '',
    'PoliticalDivision1' => '',
    'PostcodePrimaryLow' => '',
    'CountryCode' => ''
    ));

  echo $upsValidateStreet->responseXML;
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
</body>
</html>
