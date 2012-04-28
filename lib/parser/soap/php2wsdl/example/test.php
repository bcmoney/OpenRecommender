<?php

require_once("../WSDLCreator.php");

header("Content-Type: application/xml");

$test = new WSDLCreator("WSDLExample1", "http://www.yousite.com/wsdl");
$test->includeMethodsDocumentation(false);

$test->addFile("example_class.php");
$test->addFile("example_class2.php");

$test->setClassesGeneralURL("http://protung.ro");

$test->addURLToClass("example1", "http://protung.ro/examplewsdl");
$test->addURLToTypens("XMLCreator", "http://localhost/php2swdl");

$test->ignoreMethod(array("example1_1"=>"getEx"));

$test->createWSDL();

$test->printWSDL(true); // print with headers
print $test->getWSDL();
$test->downloadWSDL();
$test->saveWSDL(dirname(__FILE__)."/test.wsdl", false);



?>