<?php

require_once "../../../config.php";

class Service {

    private $service_name;
    private $url;
    
    private $api_url;
    private $api_key;
    private $secret_key;
    private $app_id;    
   
    private $query;

    function __construct($service_name, $api_url, $api_key, $secret_key, $app_id=null, $url=null) {
      (!empty($url) && is_set($url)) ? setUrl($url) : '';
      (!empty($api_url) && is_set($api_url)) ? setApiUrl($api_url) : setApiUrl($GLOBALS['api_config'][$this->service_name]['api_url'];);
      (!empty($api_key) && is_set($api_key)) ? setApiKey($api_key) : setApiKey($config[$this->service_name.'_api_key']);      
      (!empty($app_id) && is_set($app_id)) ? setAppId($app_id) : '';
      (!empty($url) && is_set($url)) ? setUrl($url) : '';
    }

    public function setApiKey($api_key) {
      $this->api_key = $api_key;
    }

    public function getApiKey() {
      return $this->prefix;
    }    
    
    public function setAppId($app_id) {
      $this->app_id = $app_id;
    }

    public function getAppId() {
      return $this->app_id;
    }

    //Link to the main URL endpoint
    public function setApiUrl($api_url) {
      $this->api_url = $api_url;
    }

    public function getApiUrl() {
      return $this->api_url;
    }

    //Link back to the regular site
    public function setUrl($url) {
      $this->url = $url;
    }
   
    public function getUrl() {
      return $this->url;
    }    
    
    public function setQuery($query) {
      $this->query = $query;
    }

    public function getQuery() {
      return $query;
    }
    
    
    /* Utility functions */
    public function makeRequest($url, $method, $body) {
      switch($method) {
        case "PUT":
          put_request_curl($url, $file, $username, $password);
          break;
        case "DELETE":
          delete_request_curl($url, $username, $password);
          break;
        case "POST":
          postRequest($url, $body);
          break;          
        default: //GET and all others get wrapped as GET
          getRequest($url);
          break;
      }
    }
    
    private function getRequest() {
      $data = '';
      try {        
        $handle = fopen($url, "r"); // Get remote content/data        
        if ($handle) {
          // some content/data was received, then read & return
          while (!feof($handle)) {
            $buffer = fgets($handle, 4096);
            $data .= $buffer;
          }
          fclose($handle);
        }
      }
      catch (Exception $e) {        
        if (function_exists('file_get_contents')) {
          $data .= file_get_contents($url);
        }
        else {
          $data .= file_get_contents_curl($url);
        }
      }
      return $data;
    }
    
    private function file_get_contents_curl($url) {
      $ch = curl_init();      
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);      
      $data = curl_exec($ch);
      curl_close($ch);      
      return $data;
    }
        
    private function postRequest($url, $body, $optional_headers=null) {
      try { 
        $params = array('http' => array(
                    'method' => 'POST',
                    'content' => $body
                  ));
        if (!empty($optional_headers)) {
          $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if (!$fp) {
          throw new Exception("Problem with $url, $php_errormsg");        
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
          throw new Exception("Problem reading data from $url, $php_errormsg");
        }
      }
      catch (Exception $e) {
        $response = post_request_curl($url, $body);
      }
      return $response;
    }    
    
    /*
     * post_request_curl
     *   Send's an HTTP Request via POST, typically for updating information on a server
     * @param url  String path to the server
     * @param body  Array containing elements to encode and pass as HTTP Request BODY String
     */
    private function post_request_curl($url, $body) {
      $ch = curl_init(); //open connection
      //url-ify the data for the POST
      $body_string = '';
      foreach($body as $key=>$value) { $body_string .= $key.'='.$value.'&'; }
      rtrim($body_string,'&');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the response instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_POST, count($body));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body_string);
        curl_setopt($ch, CURLOPT_URL, $url);
      $response = curl_exec($ch); //execute POST request
        curl_close($ch);      
      return $response;
    }
    
    
    /*
     * put_request_curl
     *   Send's an HTTP Request via PUT, typically for creating information on (i.e. sending a file to) a server.
     *   The server must support remote File Upload and BASIC Authentication (username/password)
     * @param url  String path to the server
     * @param body  Array containing elements to encode and pass as HTTP Request BODY String
     */
    private function put_request_curl($url, $file, $username, $password) {
      $fp = fopen ($file, "r");
      $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT') );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file));
      $response = curl_exec($ch);
      $error = curl_error($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);      
        if ($error) { return $error; }
      return $response;
    }
    
    /*
     * delete_request_curl
     *   Send's an HTTP Request via DELETE, typically for deleting information from a server.
     *   The server must support remote DELETE and BASIC Authentication (username/password)
     * @param url  String path to the server
     * @param body  Array containing elements to encode and pass as HTTP Request BODY String
     */
    private function delete_request_curl($url, $username, $password) {
      $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: DELETE') );
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $response = curl_exec($ch);
      $error = curl_error($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);     
        if ($error) { return $error; }
      return $response;
    }    
}

?>