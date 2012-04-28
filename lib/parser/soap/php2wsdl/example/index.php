<?php

require_once("../WSDLCreator.php");

header("Content-Type: application/xml");

$test = new WSDLCreator("RockPaperScissors", "http://www.bcmoney-mobiletv.com/services/games/rps/rps.wsdl");
$test->includeMethodsDocumentation(false);
$test->addFile("../../server.php");

$test->setClassesGeneralURL("http://bcmoney-mobiletv.com");
$test->addURLToTypens("rps", "http://bcmoney-mobiletv.com/services/games/rps/");
//$test->addURLToClass("XMLCreator", "http://localhost/php2swdl");

$test->ignoreMethod(array("rps"=>"__construct", ));

$test->createWSDL();

$test->printWSDL(true); // print the WSDL to screen (with headers)
#print $test->getWSDL(); //return the WSDL as a string so you can save it in a variable
#$test->downloadWSDL(); //force you to download a copy of the generated WSDL
#$test->saveWSDL(dirname(__FILE__)."/test.wsdl", false); //save the WSDL to the file location specified



?>