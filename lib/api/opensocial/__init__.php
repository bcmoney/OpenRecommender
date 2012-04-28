<?php
/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// Set the default timezone since many servers won't have this configured
date_default_timezone_set('America/Los_Angeles');

// Report everything, better to have stuff break here than in production
ini_set('error_reporting', E_ALL | E_STRICT);

// Add the osapi directory to the include path
set_include_path(get_include_path() . PATH_SEPARATOR . '..');

// Require the osapi library
require_once "osapi/osapi.php";

// Enable logger.
osapiLogger::setLevel(osapiLogger::INFO);
osapiLogger::setAppender(new osapiFileAppender("/tmp/logs/osapi.log"));

// Allow users to select which test they would like to run from the querystring
if (isset($_REQUEST["test"])) {
  $test = $_REQUEST["test"];
} else {
  $test = 'orkut';
}

$osapi = false;
$strictMode = false;
$userId = '@me';
$appId = '@app';

// Create an identifier for the local user's session
session_start();
$localUserId = session_id();

// Select the appropriate test and initialize
switch ($test) {
  case 'xrds':
    $userId = '45024593';
    $storage = new osapiFileStorage('/tmp/osapi');
    $provider = new osapiXrdsProvider('http://en.netlog.com/', $storage);
    $auth = osapiOAuth3Legged::performOAuthLogin('605776b05bad192d854121de477238a7', 'b63bf18647211c8fd7155331c0daedd3e', $storage, $provider, $localUserId);
    $osapi = new osapi($provider, $auth);
    break;
  case 'netlog':
    $userId = '45024593';
    $storage = new osapiFileStorage('/tmp/osapi');
    $provider = new osapiNetlogProvider();
    $auth = osapiOAuth3Legged::performOAuthLogin('605776b05bad192d854121de477238a7', 'b63bf18647211c8fd7155331c0daedd3e', $storage, $provider, $localUserId);
    $osapi = new osapi($provider, $auth);
    break;
  case 'hi5':
    $userId = '167259949';
    $storage = new osapiFileStorage('/tmp/osapi');
    $provider = new osapiHi5Provider();
    $auth = osapiOAuth3Legged::performOAuthLogin('http://test.chabotc.com/proxied.xml', 'a38336_e76c2b4365eba31c6bf9f', $storage, $provider, $localUserId);
    $osapi = new osapi($provider, $auth);
    break;
  case 'partuza':
    $provider = new osapiPartuzaProvider();
    $storage = new osapiFileStorage('/tmp/osapi');
    $auth = osapiOAuth3Legged::performOAuthLogin('ddf4f9f7-f8e7-c7d9-afe4-c6e6c8e6eec4', '6f0e1a11ac45caed32d699f9e92ae959', $storage, $provider, $localUserId);
    $osapi = new osapi($provider, $auth);
    break;
  case 'partuzaLocal':
    $provider = new osapiLocalPartuzaProvider();
    $storage = new osapiFileStorage('/tmp/osapi');
    $auth = osapiOAuth3Legged::performOAuthLogin('ddf4f9f7-f8e7-c7d9-afe4-c6e6c8e6eec4', '6f0e1a11ac45caed32d699f9e92ae959', $storage, $provider, $localUserId);
    $osapi = new osapi($provider, $auth);
    break;
  case 'partuzaLocalRest':
    $provider = new osapiLocalPartuzaProvider();
    $provider->rpcEndpoint = null;
    $storage = new osapiFileStorage('/tmp/osapi');
    $auth = osapiOAuth3Legged::performOAuthLogin('ddf4f9f7-f8e7-c7d9-afe4-c6e6c8e6eec4', '6f0e1a11ac45caed32d699f9e92ae959', $storage, $provider, $localUserId);
    $osapi = new osapi($provider, $auth);
    break;
  case 'plaxo':
    $osapi = new osapi($provider = new osapiPlaxoProvider(), osapiOAuth3Legged::performOAuthLogin('anonymous', '', new osapiFileStorage('/tmp/osapi'), $provider, $localUserId));
    break;
  case 'orkut':
    $userId = '03067092798963641994';
    $osapi = new osapi(new osapiOrkutProvider(), new osapiOAuth2Legged("orkut.com:623061448914", "uynAeXiWTisflWX99KU1D2q5", '03067092798963641994'));
    break;
  case 'orkutRest':
    // special case for testing orkut's REST end-point, instead of it's default RPC end point.. we accomplish this by using Orkut's provider, but unsetting it's rpc configuration
    $userId = '03067092798963641994';
    $provider = new osapiOrkutProvider();
    $provider->rpcEndpoint = null;
    $osapi = new osapi($provider, new osapiOAuth2Legged("orkut.com:623061448914", "uynAeXiWTisflWX99KU1D2q5", $userId));
    break;
  case 'myspace':
    $userId = '495184236';
    $osapi = new osapi(new osapiMySpaceProvider(), new osapiOAuth2Legged("http://www.myspace.com/495182150", "20ab52223e684594a8050a8bfd4b06693ba9c9183ee24e1987be87746b1b03f8", $userId));
    break;
  case 'myspaceid':
    // NOTE: to authorize using 3leggedOAuth & MySpace your app MUST be a MySpaceID app otherwise use 2LeggedOAuth
    $provider = new osapiMySpaceProvider();
    $storage = new osapiFileStorage('/tmp/osapi');
    $auth = osapiOAuth3Legged::performOAuthLogin('6043e2bda2e546498928732c01982b24', '14d70750b3ae45c1b8c8b834f145aa3fa632013885964cf6bd41792d51754810', $storage, $provider, $localUserId);
    $osapi = new osapi($provider, $auth);
    break;
  case 'google3legged':
    // See http://code.google.com/apis/accounts/docs/OAuthForInstalledApps.html for information about the key/secret
    $provider = new osapiGoogleProvider();
    $storage = new osapiFileStorage('/tmp/osapi');
    $auth = osapiOAuth3Legged::performOAuthLogin('anonymous', 'anonymous', $storage, $provider, $localUserId);
    $osapi = new osapi($provider, $auth);
    break;
  case 'google2legged':
    $userId = '101911127807751034357';
    $provider = new osapiGoogleProvider();
    $provider->rpcEndpoint = null;
    $osapi = new osapi($provider, new osapiOAuth2Legged("google.com:249475676706", "fWPcoVP6DOLVqZOF2HH+ihU2", $userId));
    break;
}

$script_name = $_SERVER["SCRIPT_NAME"];
$tests = Array("google2legged" => "Google (2 legged)", 
    "google3legged" => "Google (3 legged)", 
    "myspace" => "MySpace", 
    "myspaceid"=>"MySpaceID", 
    "orkut" => "orkut", 
    "orkutRest" => "orkut (REST)", 
    "partuza" => "Partuza", 
    "partuzaLocal" => "partuzaLocal", 
    "partuzaLocalRest" => "partuzaLocalRest", 
    "plaxo" => "Plaxo", 
    "netlog" => 'Netlog', 
    'hi5' => 'Hi5');

$links = Array();
foreach ($tests as $value => $name) {
  if ($value == $test) {
    $links[] = "<strong>$name</strong>";
  } else {
    $links[] = "<a href='$script_name?test=$value'>$name</a>";
  }
}

?>
<p><a href="index.php">Back to the index</a>. Run this sample using data from: <?=implode($links, ", ")?> </p>
