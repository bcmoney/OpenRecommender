<?php
  
  // iSpeech PHP Script (2011-12-07), version 0.3 (beta)
  // Requires the cURL PHP extension
  // Designed for cloud-based speech synthesis and speech recognition
  // For more information, visit: http://www.ispeech.org/api
  
  class iSpeechBase {
    var $server;
    var $parameters = array("device-type", "php-SDK-0.3");

    function setParameter($parameter, $value) {
      if ($parameter == "server") {
          $this->server = $value;
      }
      else {
          $this->parameters["$parameter"] = $value;
      }
    }

    function makeRequest() {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->server . "/?" . http_build_query($this->parameters));
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      
      ob_start();
      curl_exec($ch);
      $http_body = ob_get_contents();
      ob_end_clean();
      
      if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
        if ($this->parameters["action"] == "convert") {
          return array("error" => $http_body);
        }
      }
        
      return $http_body;
    }
  }
  
  class SpeechSynthesizer extends iSpeechBase {
    function __construct() {
      parent::setParameter("action", "convert");
    }
  }
      
  class SpeechRecognizer extends iSpeechBase {
    function __construct() {
      parent::setParameter("action", "recognize");    
    }
  }