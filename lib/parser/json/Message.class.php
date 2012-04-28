<?php

class MessageJSON {
  public $json;
	
  public function __construct($url) {
    if (!empty($url) && isset($url)) {
      $data = file_get_contents($url);
      $content = utf8_encode($data);
      $this->json = json_decode($content);
    }
  }
  
  //MESSAGE
  public function getMessage() {
    return $this->json->message;
  }
  public function getMessageId($message) {
    return $message->id;
  }
  public function getMessageAction($message) {
    return $message->action;
  }  
  
  //SERVICE
  public function getService($message) {
    return $message->service;
  }
  public function getServiceName($service) {
    return $service->name;
  }
  public function getServiceEndpoint($service) {
    return $service->endpoint;
  }

  //PARAMETER 
  public function getParameter($service) {
    return $service->parameter;
  }
  public function getParameterId($parameter) {
    return $parameter->id;
  }
  public function getParameterName($parameter) {
    return $parameter->name;
  }
  public function getParameterValue($parameter) {
    return $parameter->value;
  }	
  public function getParameterUnit($parameter) {
    return $parameter->unit;
  }
  public function getParameterUnitType($parameter) {
    return $parameter->unitType;
  }  
}
  
?>