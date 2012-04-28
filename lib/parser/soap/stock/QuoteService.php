<?php

class QuoteService {

  private $quotes = array("ibm" => 98.42);
  
  function getQuote($symbol) {
    if (isset($this->quotes[$symbol])) {
      return $this->quotes[$symbol];
    } else {
      throw new SoapFault("Server","Unknown Symbol '$symbol'.");
    }
  }
}

$server = new SoapServer("stockquote2.wsdl");
$server->setClass("QuoteService");
$server->handle();

?>
