<?php

require_once ("../Service.class.php");

class WorldWeather extends Service {

    private $location;
  
    public function __construct($key, $app) {
        parent::__construct();
    }
    
    public function setLocation($location) {
      $this->location = $location;
    }
    
    public function getLocation() {
      return $this->location;
    }
    
    public function getWeather() {
      parent::makeRequest(parent::getApiUrl());
    }
    
}

  
?>