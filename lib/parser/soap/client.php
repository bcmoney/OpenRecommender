<?php

class AuthHeader {
  public $username;
  public $password;
  public $encoded_auth_header;
  
  function __construct() {
		$this->encoded_auth_header = base64_encode("{$this->username}:{$this->password}");
	}
}

//build Authentication headers   
$AuthHeader = new AuthHeader("bcmoney", "tmppass");
  
//setup Client request based on WSDL
$client = new SoapClient("http://bcmoney-mobiletv.com/api/soap/RockPaperScissors/RockPaperScissors.wsdl", array("exceptions"=>true));
  $headers[] = new SoapHeader('http://bcmoney-mobiletv.com/', 'AuthHeader', $AuthHeader);
  $client->__setSoapHeaders($headers);
$result = $client->playGame(array("choice"=>"Rock"));

//DEBUG:  
echo "<pre>"; print_r($result); echo "</pre>";

?>
