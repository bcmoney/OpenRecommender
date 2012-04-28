<?php
/*
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

/*
  require_once('Space.php');
  $key = 'http://www.myspace.com/xxxxx';
  $secret = 'xxxxxxxxxxx';
  $s = new Space($key,$secret);
  $hProfile = $s->profile(1234567890);
*/


require_once('../../../../../api/authorization/oauth/OAuth.php');

class Space {
  private $sServer  = 'http://api.msappspace.com/';
  //private $sExt     = '.json';
  private $sExt     = '.xml';
  private $sVersion = 'v1/';

  private $sKey;
  private $sSecret;
  private $oaConsumer;
  private $oaToken;

  private $bProfile = true;

  
  public function __construct($sKey,$sSecret) {
    $this->sKey    = $sKey;
    $this->sSecret = $sSecret;
    $this->oaConsumer = new OAuthConsumer($this->sKey,$this->sSecret);
    $this->oaToken    = new OAuthToken(null,null);
  }

  public function currentUser() {
    $sQuery = 'currentuser';
    return $this->do_request($sQuery);
  }

  public function user($iUser) {
    $sQuery = 'users/'.$iUser;
    return $this->do_request($sQuery);
  }

  public function profile($iUser) {
    $sQuery = 'users/'.$iUser.'/profile';
    return $this->do_request($sQuery);
  }

  public function friends($iUser,$page = null,$page_size = null,$list = null) {
    $sQuery = 'users/'.$iUser.'/friends';
    $hParams = array();
    if( $page !== null ) {
      $hParams['page'] = $page;
    }

    if( $page_size !== null ) {
      $hParams['page_size'] = $page;
    }

    if( $list !== null ) {
      $hParams['list'] = $page;
    }
    return $this->do_request($sQuery,$hParams);
  }

  public function friendship($iUser,$aIds) {
    $sQuery = 'users/'.$iUser.'/friends/'.implode($aIds,';');
    return $this->do_request($sQuery);
  }

  public function albums($iUser) {
    $sQuery = 'users/'.$iUser.'/albums';
    return $this->do_request($sQuery);
  }

  public function album($iUser,$iAlbum) {
    $sQuery = 'users/'.$iUser.'/albums/'.$iAlbum.'/photos';
    return $this->do_request($sQuery);
  }

  public function photos($iUser) {
    $sQuery = 'users/'.$iUser.'/photos';
    return $this->do_request($sQuery);
  }

  public function photo($iUser,$iPhoto) {
    $sQuery = 'users/'.$iUser.'/photos/'.$iPhoto;
    return $this->do_request($sQuery);
  }

  public function interests($iUser) {
    $sQuery = 'users/'.$iUser.'/interests';
    return $this->do_request($sQuery);
  }

  public function details($iUser) {
    $sQuery = 'users/'.$iUser.'/details';
    return $this->do_request($sQuery);
  }

  public function videos($iUser) {
    $sQuery = 'users/'.$iUser.'/videos';
    return $this->do_request($sQuery);
  }

  public function video($iUser,$iVideo) {
    $sQuery = 'users/'.$iUser.'/videos/'.$iVideo;
    return $this->do_request($sQuery);
  }

  public function groups($iUser) {
    $sQuery = 'users/'.$iUser.'/groups';
    return $this->do_request($sQuery);
  }

  public function status($iUser) {
    $sQuery = 'users/'.$iUser.'/status';
    return $this->do_request($sQuery);
  }

  public function mood($iUser) {
    $sQuery = 'users/'.$iUser.'/mood';
    return $this->do_request($sQuery);
  }

  private function do_request($sQuery,$hParams = array()) {
    if( $this->bProfile ) {
    $s = microtime(true);
    $r = $this->_do_request($sQuery,$hParams);
    $e = microtime(true);

    self::$aCalls[] = array( 'query' => $sQuery, 'time' => ($e-$s) );
    self::$iTotal  == ($e-$s);

    return $r;
    } else {
      return $this->_do_request($sQuery,$hParams);
    }
  }

  private function _do_request($sQuery,$hParams = array()) {
    $sURL = $this->sServer.$this->sVersion.$sQuery.$this->sExt;
    $sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
    $r = OAuthRequest::from_consumer_and_token($this->oaConsumer,$this->oaToken,'GET',$sURL,$hParams);

    $r->sign_request($sha1_method, $this->oaConsumer, NULL);
    $sURL = $r->to_url();

    if (function_exists('curl_init')) {
      // Use CURL if installed...
      $oCurl = curl_init();
      curl_setopt($oCurl, CURLOPT_URL, $sURL);
      curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($oCurl, CURLOPT_USERAGENT, 'mySpace API PHP5 Client 0.1 (curl) ' . phpversion());
      $sContent = curl_exec($oCurl);
      $code = curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
      curl_close($oCurl);
    } else {
      // Note: this needs remote file enabled in php.ini
      $sContent = file_get_contents($r->to_url());
    }
 
    if( $this->sExt == '.json' ) {
      return json_decode($sContent);
    } else if( $this->sExt == '.xml' ) {
      $oXML = @simplexml_load_string($sContent); // Warnings for "invalid" xmlns
      $hResult = self::convert_simplexml_to_array($oXML);

      if( $code && $code >= 400 ) {
        throw new Exception($code.' '.$hResult['BODY']['DIV']['P']);
      }
      return $hResult;
    }
  }

  public static function convert_simplexml_to_array($sxml) {
    $arr = array();
    if ($sxml) {
      foreach ($sxml as $k => $v) {
        if ($sxml['count']) {
          $arr[] = self::convert_simplexml_to_array($v);
        } else {
          $arr[$k] = self::convert_simplexml_to_array($v);
        }
      }
    }
    if (sizeof($arr) > 0) {
      return $arr;
    } else {
      return (string)$sxml;
    }
  }


  static private $aCalls = array();
  static private $iTotal = 0;

  static public function get_calls() {
    return self::$aCalls;
  }

  static public function get_number_calls() {
    return sizeof(self::$aCalls);
  }

  static public function get_time() {
    return self::$iTotal;
  }

}

?>