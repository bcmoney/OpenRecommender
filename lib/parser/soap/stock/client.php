<?php

  $client = new SoapClient("stockquote2.wsdl");

  print($client->getQuote("ibm"));

?>