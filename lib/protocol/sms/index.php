<?php

require_once "Email2SMS.class.php";

const EMAIL_DATABASE = "email2sms.db";
$DEBUG = true;

/* Get input values for sending an SMS */
$country = (!empty($_REQUEST['country'])) ? $_REQUEST['country'] : "Canada";
$carrier = (!empty($_REQUEST['carrier'])) ? $_REQUEST['carrier'] : "Bell Mobility";
$subject = (!empty($_REQUEST['subject'])) ? $_REQUEST['subject'] : "SMS";
$from_number = (!empty($_REQUEST['from'])) ? $_REQUEST['from'] : "9029997603";
$to_number = (!empty($_REQUEST['to'])) ? $_REQUEST['to'] : "9024836349";
$message = (!empty($_REQUEST['msg'])) ? $_REQUEST['msg'] : "SMS text goes here";

$email = (!empty($_REQUEST['email'])) ? $_REQUEST['email'] : "bc@bcmoney-mobiletv.com";


// Creating Object
$email2sms_obj = new Email2SMS($email, EMAIL_DATABASE);

// Setting Country and carrier information
$email2sms_obj->setProperty('email2smsCountry', $country);
$email2sms_obj->setProperty('email2smsCarrier', $carrier);
$email2sms_obj->setProperty('email2smsSubject', $subject);
$email2sms_obj->setProperty('email2smsNumber' , $to_number);

// Setting SMS text
$email2sms_obj->setProperty('email2smsText'   , $message);

// Voila, SMS sent!
$email2sms_obj->prepareAndSendSMS();


// output errors (if any)
if($DEBUG) {
  echo $email2sms_obj->getError();
  echo "<pre>"; print_r($email2sms_obj->getError('all')); echo "</pre>"; 
}

?>