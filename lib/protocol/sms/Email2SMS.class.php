<?php

  /**
   * Email2SMS API
   *
   *
   * This library provides generic API for Email2SMS gateway service in an uniform way.
   * This class would enable you to send a SMS via Carrier Email2SMS Gateway
   * 
   * This class provides functionality to send FREE SMS via Email2SMS gateways on various supported
   * mobile carriers network across many countries.
   *
   *
   *
   * @package     Email2SMS API
   * @version     1.0
   * @category    Library
   * @author      Utsav Handa < handautsav at hotmail dot com >
   * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
   *
   *
   * !! ~ Made in India ~ !!
   *
   *
   * @changelog 
   * -- 2009-10-06  -    Vision & Initial Implementation 
   *
   *
   * @todo
   * -- Document usage more elaborately
   * -- Add HTML SMS capability
   *
   *
   *
   * ALSO SEE - README
   *
   */


  /** License
   *
   * Copyright (c) 2009 Utsav Handa <handautsav at hotmail dot com>
   *
   * Permission is hereby granted, free of charge, to any person obtaining a copy of this
   * software and associated documentation files (the "Software"), to deal in the Software 
   * without restriction, including without limitation the rights to use, copy, modify, 
   * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to 
   * permit persons to whom the Software is furnished to do so, subject to the following
   * conditions:
   *
   * The above copyright notice and this permission notice shall be included in all copies
   * or substantial portions of the Software.
   *
   * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
   * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
   * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE 
   * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR 
   * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
   * IN THE SOFTWARE.
   */


  /**
   * Usage Example ::
   *
   * require_once('class-email2sms.php');
   *
   * $email2sms_obj = new Email2SMS('from@domain.com', 'email2sms.db');
   * $email2sms_obj -> setProperty('email2smsCountry', 'India');
   * $email2sms_obj -> setProperty('email2smsCarrier', 'Andhra Pradesh Airtel');
   * $email2sms_obj -> setProperty('email2smsNumber' , '9810112345');
   * $email2sms_obj -> setProperty('email2smsSubject', 'SMS');
   * $email2sms_obj -> setProperty('email2smsText'   , 'SMS text goes here');
   * $email2sms_obj -> prepareAndSendSMS();
   * print_r($email2sms_obj -> getError('all'));
   *
   *
   *
   **/



/** Class- Email2SMS Gateway  */
class Email2SMS {
  
  /** Variable(s) */
  var $_lasterror = NULL;
  var $_errors    = array();
  
  var $_email2smsConfig = array(
                                'email2smsText'     => NULL,           /** SMS Content                 */
                                'email2smsFrom'     => NULL,           /** Email2SMS From              */
                                'email2smsSubject'  => 'SMS',          /** Email2SMS Subject           */
                                'email2smsCountry'  => NULL,           /** Default SMS Carrier Country */
                                'email2smsCarrier'  => NULL,           /** SMS Carrier                 */
                                'email2smsCarriers' => array(),        /** SMS Carriers                */
                                'email2smsNumber'   => NULL,           /** SMS Number                  */
                                'email2smsMaxChars' => 160,            /** Max. SMS Characters         */
                                'email2smsType'     => 'text',         /** SMS Type                    */
                                'email2smsDB'       => array(),        /** Email2SMS Gateway info      */
                                'email2smsDB'           => 'email2sms.db',  /** Email2SMS Gateway info DB       */
                                'email2smsDBIdentifier' => 'NUMBER',        /** Email2SMS DB Number Identifier  */
                                );
  
  
  
  /**
   * Object Constructur
   *
   * @param  string    $gwDB
   * @param  string    $smsFrom
   * @param  string    $smsSubject
   * @param  string    $country
   * @param  string    $carrier
   * @param  digit     $number
   *
   * @return TRUE
   */
  function Email2SMS($smsFrom = NULL, $gwDB = NULL, $smsSubject = NULL, $country = NULL, $carrier = NULL, $number = NULL) {
    

    /** Default Object Properties */
    ( $gwDB       && $this -> setProperty('email2smsDB',      $gwDB)           );
    ( $smsFrom    && $this -> setProperty('email2smsFrom',    $smsFrom)        );
    ( $smsSubject && $this -> setProperty('email2smsSubject', $smsSubject)     );
    ( $country    && $this -> setProperty('email2smsCountry', $country)        );
    ( $carrier    && $this -> setProperty('email2smsCarrier', $carrier)        );
    ( $number     && $this -> setProperty('email2smsNumber',  $number)         );

    /** Load email2SMS Gateway information */
    $this->_getEmail2SMSInformation('loadEmail2SMSDB');


    return TRUE;
  }
  
  

  /**
   * 'SET' property
   *
   * @param  string    $property
   * @param  mixed     $mixed_value
   *
   * @return TRUE/FALSE
   */
  function setProperty($property, $mixed_value) {
    
    /** Validate Property */
    if ( !array_key_exists($property, $this->_email2smsConfig) ) {
      $this->_setError("Property '$property' specified is not supported");
      return FALSE;
    }
    
    /** Process 'Value' */
    if (is_array($this->_email2smsConfig[$property])) {
      /**foreach ($mixed_value as $key => $value) { $this->_email2smsConfig[$property][$key] = $value; } */
      array_push($this->_email2smsConfig[$property], $mixed_value);
      //$this->_setError("Value specified for Property '$property' does not accept 'array' of values");
      //return FALSE;
    } else {
      $this->_email2smsConfig[$property] = trim($mixed_value);
    }
    
    return TRUE;
  }
  
  
  /**
   * 'GET' property
   *
   * @param  string    $property
   *
   * @return @Property value
   */
  function getProperty($property) {
    
    return ( array_key_exists($property, $this->_email2smsConfig) ? $this->_email2smsConfig[$property] : false );
  }

  
  /**
   * Prepare and send SMS
   *
   * @param  string   $smsContent
   *
   * @return TRUE/FALSE
   */
  function prepareAndSendSMS($smsContent = NULL) {

    
    /************* Validate 'email2smsFields' *************/
    $validate_fields = array('From' => 'email2smsFrom', 'Subject' => 'email2smsSubject', 'Country' => 'email2smsCountry');
    foreach ($validate_fields as $key => $val) {
      if ($this->_email2smsConfig[$val] == NULL) {
        $this->_setError("SMS '$key' field should be SET before sending SMS");
        return FALSE;
      }
    }
    /************* Validate 'email2smsFields' *************/

    /************* Validate 'smsContent' *************/
    $smsContent = trim($smsContent);
    /** Process 'smsContent' */
    if ($smsContent == '') {
      $smsContent = $this->_email2smsConfig['email2smsText'];
    } 
    /** Prepare SMS Content */
    $smsContent = $this -> _prepareSMSContent($smsContent);

    /** Check 'email2smsText' */
    if (strlen($smsContent) <= 0) {
      $this->_setError('SMS Content should not be blank');
      return FALSE;

    } elseif (strlen($smsContent) > $this->_email2smsConfig['email2smsMaxChars']) {
      $this->_setError("SMS Content should not exceed defined maximum (".$this->_email2smsConfig['email2smsMaxChars'].") characters");
      return FALSE;

    }
    /************* Validate 'smsContent' *************/
    
    /************* Validate 'email2smsCarrier(s)' *************/
    if ($this->_email2smsConfig['email2smsCarrier'] && $this->_email2smsConfig['email2smsNumber']) {
      array_push($this->_email2smsConfig['email2smsCarriers'], array($this->_email2smsConfig['email2smsCarrier'] => $this->_email2smsConfig['email2smsNumber']));
    }
    if ( count($this->_email2smsConfig['email2smsCarriers']) <= 0 ) {
      $this->_setError('SMS Carrier shoud be specified');
      return FALSE;
    }
    /** Check Country */
    if (!$this->_getEmail2SMSInformation('checkCountry', $this->_email2smsConfig['email2smsCountry'])) {
      $this->_setError('SMS Country '.$this->_email2smsConfig['email2smsCountry'].' is currently not supported');
      return FALSE;
    }
    /**  Check Carrier(s) */
    foreach ($this->_email2smsConfig['email2smsCarriers'] as $cnt => $carriernumbers) {
      if (is_array($carriernumbers)) {
        foreach ($carriernumbers as $carrier => $number) {
          if (!$this->_getEmail2SMSInformation('checkCarrier', $this->_email2smsConfig['email2smsCountry'], $carrier)) {
            $this->_setError('SMS Country ('.$this->_email2smsConfig['email2smsCountry'].') Carrier ('.$carrier.') specified is currently not supported');
            return FALSE;
          }
        }
      } else {
        $this->_setError('SMS Carrier/Number in UNKOWN format');
        return FALSE;
      }
    }
    /************* Validate 'email2smsCarrier(s)' *************/


    /** Send */
    return $this -> _sendActualSMS($smsContent);
  }

  
  /**
   * 'GET' error message 
   *
   * @return ERRORS
   */
  function getError($cmd = 'lasterror') {    
    return ( ($cmd == 'all') ? $this->_errors : $this->_lasterror );
  }
  
  
  /******************************************************************************************************/
  /***************************************** HELPER FUNCTION(s) *****************************************/
  /******************************************************************************************************/


  /**
   * Prepare and send SMS
   *
   * @param  string   $smsContent
   *
   * @return TRUE/FALSE
   */
  function _sendActualSMS($smsContent) {

    /** Send SMS */
    foreach ($this->_email2smsConfig['email2smsCarriers'] as $cnt => $carriernumbers) {
      foreach ($carriernumbers as $carrier => $number) {
        $to         = str_replace($this->_email2smsConfig['email2smsDBIdentifier'], $number, $this->_getEmail2SMSInformation('checkCarrier', $this->_email2smsConfig['email2smsCountry'], $carrier));
        $subject    = $this->_email2smsConfig['email2smsSubject'];   //Subject
        $message    = $smsContent;                                   //SMS
        $headers    = 'From:bc@bcmoney-mobiletv.com';
        $parameters = '-f'.$this->_email2smsConfig['email2smsFrom'];


        /** We will be submit the 'email' to be sent. */
        $mail_status = mail($to, $subject, $message, $headers, $parameters);
        
        /** Mail sending status */
        if (! $mail_status) {
          $this->_setError("Email sending failed for recipient '$number' ");
        }
        
      }
    }
    
    
    return TRUE;
  }
  

  /**
   * Prepare SMS for sending
   *
   * @param  string   $smsText
   *
   * @return prepared sms content
   */
   function _prepareSMSContent($smsText) {
     
     /** smsText would be prepared for special devices (cell-phones).
         Some older cell-phones aren't SMART and thus don't understand
         'NewLine' character */
     $smsText = preg_replace(array('/\n+/', '/\n/'), array("\n", '\0'), $smsText);
     
     
     return $smsText;
   }
   

  /**
   * 'SET' error message 
   *
   * @param  string   $errorText
   *
   * @return FALSE
   */
   function _setError($errorText) {
     
     // Set error 
     $this->_errors[]  = $this->_lasterror = $errorText;
     //print "\n ".$errorText;

     return FALSE;
   }



   /**
   * Process all the information related to Email2SMS gateways 
   *
   * @param  string   $cmd
   * @param  string   $country
   * @param  string   $carrier
   *
   * @return TRUE/FALSE/INFO
   */
   function _getEmail2SMSInformation($cmd = 'checkCarrier', $country = NULL, $carrier = NULL) {
     /** This function returns information about 'Email2SMS' gateways */
     

     /** Command 'loadEmail2SMSDB' - loads Email2SMS gateway information */
     if ($cmd == 'loadEmail2SMSDB') {

       /** Load file */
       $this->_email2smsConfig['email2smsDB'] = $this->_parse_ini_file_extended($this->_email2smsConfig['email2smsDB']);

       /** Check loaded database */
       if (count($this->_email2smsConfig['email2smsDB']) <= 0) {
         $this->_setError("ERROR loading Email2SMS gateway DB with for '" . $this->_email2smsConfig['email2smsDB'] . "' DB file. ");
       }
       
       return 1;
     }


     /** Sanitizing information */
     $this -> _email2smsConfig['email2smsDB'] = array_change_key_case($this -> _email2smsConfig['email2smsDB']);
     $country             = strtolower(str_replace(' ', '', $country));
     $carrier             = strtolower(str_replace(' ', '', $carrier));


     /** Command 'checkCountry' - checks country */
     if ($cmd == 'checkCountry') {
       return ( in_array($country, array_keys($this -> _email2smsConfig['email2smsDB'])) ? TRUE : FALSE );
     }
     /** Command 'checkCarrier' - checks carrier */
     if ($cmd == 'checkCarrier') {
       /** Check Carrier */
       foreach ($this -> _email2smsConfig['email2smsDB'][$country] as $carriername => $carrierinfo) {
         if (str_replace(' ', '', strtolower($carriername)) == $carrier) {
           return $carrierinfo;
         }
       }
     }
     


     return FALSE;
   }




   /**
    * Parses INI file adding extends functionality via ":base" postfix on namespace.
    *
    * @param  string    $file
    * @param  boolean   $parse_extended
    * @param  string    $commentcharregex
    * @param  char      $key_value_delimiter
    *
    * @return array
    */
   function _parse_ini_file_extended($file, $parse_extended = FALSE, $commentcharregex = '^(;|#)(.*?)', $key_value_delimiter = '=') {
  
     /** Reading file contents */
     $file_contents = @file($file);
  
     /** Var def. */
     $parsed_ini_content = array();
     $section = '';
  
     /** Check contents */
     if ( (! is_array($file_contents)) || (count($file_contents)<=0) ) { 
       return $parsed_ini_content; 
     }

  
     /** Scan each line of 'file' and process */
     foreach ($file_contents as $file_line) {
       $file_line = trim($file_line);
    
       /** Do not process "blank" & "comment" line */
       if ( ($file_line == '') || (preg_match_all("/$commentcharregex/", $file_line, $matches)) ) {
         continue;
       }
    
       /** Checking for [section] */
       if (preg_match('/^\[(.*?)\]$/', $file_line, $matches)) {
         $section                      = trim($matches[1]);
         $parsed_ini_content[$section] = array();
      
         continue; /** loop next line */
       }
    
       /** We will pick-up keys and values and add it to section */
       /** There may be chances that 'key_value_delimiter' is missing. */
       @list($key, $value) = @explode($key_value_delimiter, $file_line, 2);
       /** Add 'section' "key-value" */
       $parsed_ini_content[$section][trim($key)] = trim($value);
     }
  

     /** We will be extending the 'ini parsing' */
     if ($parse_extended) {
    
       /** Loop through each 'namespace' key and check for 'clubbed'
           properties */
       foreach($parsed_ini_content as $key => $properties){
      
         /** Extract 'namespace' and 'extended' property */
         @list($extends, $name) = @explode(':', $key, 2);
         $name = trim($name); $extends = trim($extends);
      
         /** We will not process 'un-extended' key */
         if ($name == '' )  continue;
      
         /** Inherit base namespace */
         $parsed_ini_content[$extends][$name] = $properties;
         unset($parsed_ini_content[$key]);
       }

     }
  
  
     return $parsed_ini_content;
   }

  

  }



?>