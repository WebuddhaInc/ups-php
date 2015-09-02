<?php

namespace UPS;

class Connector {

	var $License;
	var $User;
	var $Pass;
	var $templatePath;
	var $debugMode;
	var $accessRequest;

    /**********************************************
     * $License = XML Access Code provided by UPS
     * $User = UPS.com Username
     * $Password = UPS.com Password
     * $templatePath = Path to XML templates
     *
     **********************************************/

	function __construct($license,$user,$pass){
		$this->License = $license;
		$this->User = $user;
		$this->Pass = $pass;
		$this->setTestingMode(1);
		$this->accessRequest = false;
		$this->templatePath = 'xml/'; // No beginning slash if path is relative
	}

	function access(){
		// This will create the AccessRequest XML that belongs at the beginning of EVERY request made to UPS
		$accessXML = $this->sandwich($this->templatePath.'AccessRequest.xml', array('{LICENSE}','{USER_ID}','{PASSWORD}'), array($this->License,$this->User,$this->Pass));
		$this->accessRequest = true;
		return $accessXML;
	}

	function request($type, $xml){
		// This function will return all of the relevant response info in the form of an Array
		if ($this->accessRequest != true) {
			die('access function has not been set');
		} else {
			$output = preg_replace('/[\s+]{2,}/', '', $xml);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->upsUrl.'/ups.app/xml/'.$type);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $output);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$curlReturned = curl_exec($ch);
			curl_close($ch);

			// Find out if the UPS service is down
			preg_match_all('/HTTP\/1\.\d\s(\d+)/',$curlReturned,$matches);
			foreach($matches[1] as $key=>$value) {
				if ($value != 100 && $value != 200) {
					$this->throwError("The UPS service seems to be down with HTTP/1.1 $value");
					return false;
            	} else {
					$response = strstr($curlReturned, '<?'); // Seperate the html header and the actual XML because we turned CURLOPT_HEADER to 1
					return $response;
				}
			}
		}
	}

	function sandwich($templateFile, $findArray, $replaceArray){
		// This will look in the template folder for an xml template and subsitute one array for another
		$handle=fopen($templateFile, "r");
		if($handle){
				$buffer = fread($handle, filesize($templateFile));fclose($handle);
		}
		$x=0;while($x < count($findArray)){
				$buffer = str_replace($findArray[$x],$replaceArray[$x],$buffer);++$x;
		}
		return $buffer;
	}

	function getAvailableLayout($templateFile){
		// This function needs commented
		$handle=fopen($templateFile, "r");
		if($handle){
			$buffer = fread($handle, filesize($templateFile));fclose($handle);
		}
		preg_match_all("/(\{.*?\})/",$buffer,$availArr);
		$lines = file($templateFile);
		$items = split(' ',$lines[1]);
		$prefix = str_replace(array('<','>',"\n","\r"),'',$items[0]);
		$x=0;
		$finalArr = array();
		$textArrayLayout .= "$".$prefix." = array();<br>";
		while($x<count($availArr[0])){
			if(!in_array($availArr[0][$x], $finalArr)){
				$finalArr[] = $availArr[0][$x];
				$key = $availArr[0][$x];
				$textArrayLayout .= "$".$prefix."['".$key."'] = '';<br>";
			}
		++$x;
		}
	return $textArrayLayout;
	}

	function setTemplatePath($path){
		// TODO: set the default path to ../xml/ incase user doesn't set it
		// Set the template path for xml templates
		if($path !== ''){
			$this->templatePath = $path;
		}
		return true;
	}

	function setTestingMode($bool){
		if($bool == 1){
			$this->debugMode = true;
			$this->upsUrl = 'https://wwwcie.ups.com'; // Don't put a trailing slash here or world will collide.
		}else{
			$this->debugMode = false;
			$this->upsUrl = 'https://www.ups.com';
		}
		return true;
	}

	function throwError($error) {
		if($this->debugMode) {
			die($error);
		}else{
			return $error;
		}
	}

}
