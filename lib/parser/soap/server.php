<?php

/**
 * RockPaperScissors.class.php 
 *
 *   Rock Paper Scissors (rps) Game server with optional SOAP encoding type
 *   (renamed to "server.php" for demo purposes of SOAP client/server)
 *
 * @author bcmoney
 * @version 1.0
 * @date 2010-10-10
 * @url http://bcmoney-mobiletv.com/api/soap/RockPaperScissors
 */

class rps {

  protected $class_name = '';
  private $authenticated;

  
	function __construct($class_name=NULL) {
		session_start();
    $this->class_name = $class_name;
	}    
 
  
	/**
   * getCurrentDateTime
	 *   Get current date and time in "dd-MM-yyyy hh:mm:ss" format
	 * @return DateTime
	 */
	function getCurrentDateTime() {
		$response = new DateTime();
    return new SoapParam($response, 'result');
	}
  
	/**
   * checkWin
	 *   Checks a Rock-Paper-Scissors game outcome for winners
	 * @param string $choice
	 * @param string $format
	 * @return string
	 */	
	protected function checkWin($choice, $format='html') {
		$Choosefrom = array("Rock", "Paper", "Scissors");
		$Choice = rand(0,2);
		$Computer = $Choosefrom[$Choice];

		$result = ''; 
    $score = '';
		$user_choice = $choice;	
    
		if ($user_choice == $Computer) {
      $result = 'Draw';
      $points = '+0';
      $_SESSION['Score']= (int)$_SESSION['Score'];
		}
		else if ($user_choice == 'Rock' && $Computer == 'Scissors') {
      $result = 'Win';
      $points = '+1';
      $_SESSION['Score']= (int)$_SESSION['Score'] +1;
		}
    else if ($user_choice == 'Rock' && $Computer == 'Paper') {
      $result = 'Lose';
      $points = '-1';
      $_SESSION['Score']= (int)$_SESSION['Score'] -1;
		}
		else if ($user_choice == 'Scissors' && $Computer == 'Rock') {
      $result = 'Lose';
      $points = '-1';
      $_SESSION['Score']= (int)$_SESSION['Score'] -1;
		}
		else if ($user_choice == 'Scissors' && $Computer == 'Paper') {
      $result = 'Win';
      $points = '+1';
      $_SESSION['Score']= (int)$_SESSION['Score'] +1;
		}
		else if ($user_choice == 'Paper' && $Computer == 'Rock') {
      $result = 'Win';
      $points = '+1';
      $_SESSION['Score']= (int)$_SESSION['Score'] +1;
		}
		else if ($user_choice == 'Paper' && $Computer == 'Scissors') {
      $result = 'Lose';
      $points = '-1';
      $_SESSION['Score']= (int)$_SESSION['Score'] -1;
		}
		
    //format result as HTML or SOAP
		if ($format == 'soap') {
      header("Content-Type: text/xml; charset=utf-8");
		  $score = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rps="http://bcmoney-mobiletv.com/api/soap/RockPaperScissors/RockPaperScissors.wsdl"><soap:Header /><soap:Body><rps:game><rps:choice>'.$user_choice.'</rps:choice><rps:opponent>'.$Computer.'</rps:opponent><rps:result>'.$result.'</rps:result><rps:points>'.$points.'</rps:points><rps:score>'.$_SESSION['Score'].'</rps:score></rps:game></soap:Body></soap:Envelope>';
		}
		else {
      header("Content-Type: text/html; charset=utf-8");
		  $score = '<strong>Computer chose: ' . $Computer . '</strong><br/>Result: ' . $result . '<br/>Your score is currently: '.$_SESSION['Score'].'<br/> <a href="server.php?format=html" target="_self">Play Again ?</a>';
		}
    
		return $score;
	}
	
	/**
	 * Plays a new game of Rock-Paper-Scissors
	 *
	 * @param string $choice
	 * @return 
	 */		
	public function playGame($choice=NULL) {
		$user_choice = (isset($_REQUEST['choice']) && !empty($_REQUEST['choice']) ? $_REQUEST['choice'] : $choice);
		if($_REQUEST['format'] == 'soap') {			
			echo $this->checkWin($user_choice, 'soap');
		}
		else if (isset($user_choice) && !empty($user_choice) && $user_choice != NULL) {
			echo $this->checkWin($user_choice, 'html');
		}
		else {
		  echo '<form method="POST" style="width:100%;text-align:center;margin:0 auto;"><fieldset><legend>Choose your weapon:</legend>
			<div style="float:left;width:99%;">      
        <div style="float:left;width:33%"><button type="submit" style="border:2px solid white;cursor:pointer;" onmouseover="this.style.border=\'2px solid goldenrod\';" onmouseout="this.style.border=\'2px solid white\';" name="choice" value="Rock" title="Rock"><img src="rock.png" alt="Rock" width="100%" /></button></div>
        <div style="float:left;width:33%"><button type="submit" style="border:2px solid white;cursor:pointer;" onmouseover="this.style.border=\'2px solid goldenrod\';" onmouseout="this.style.border=\'2px solid white\';" name="choice" value="Paper" title="Paper"><img src="paper.png" alt="Paper" width="100%" /></button></div>
        <div style="float:left;width:33%"><button type="submit" style="border:2px solid white;cursor:pointer;" onmouseover="this.style.border=\'2px solid goldenrod\';" onmouseout="this.style.border=\'2px solid white\';" name="choice" value="Scissors" title="Scissors"><img src="scissors.png" alt="Scissors" width="100%" /></button></div>
      </div>        
      <br/><br/></fieldset></form>';
		}
	}
  
  
  /*
   * Authenticate request
   */
  function authenticate($header) {
    if ((isset($header->Username)) && (isset($header->Password))) {
      if ($header->Username == "bcmoney" && $header->Password == "tmppass") {
        $this->authenticated = true;
      }
    }
  }

  /*
   * getCurrentScores
   *   Require authentication before performing request and displaying list of session IDs for all current scores
   * @param request XML  input request containing Username and Password headers
   * @return scores XML  list of active Sessions/Scores for the Web Service
   * @require Authentication      
   */
  function getCurrentScores($request) {  
  // show list of all currently active games
    return '<scores><score id="1" /><score id="2" /><score id="n" /></scores>';
  }
   
  /*
   * getScore   
   *   Require authentication before performing request and displaying all details for a specific score
   * @param request XML  input request containing Username and Password headers
   * @return scores XML  list of active Sessions/Scores for the Web Service
   * @require Authentication      
   */
  function getScore($request) {
    // show this sessions's full score details (last played choice by User/Computer, User overall +/-, User choice frequency for Rock/Paper/Scissors)
    return '<score><id>1</id><lastGame>2012-04-10T21:32:52</lastGame><player><points>+2</points><lastChoice>Rock</lastChoice><rock>4</rock><paper>1</paper><scissors>2</scissors></player><computer><points>-2</points><lastChoice>Paper</lastChoice><rock>2</rock><paper>1</paper><scissors>4</scissors></computer></score>';
  }  
  
  /*
   * answer client requests to "Autneticated methods"
   */
  public function __call($method_name, $arguments) {
    if(!method_exists($this->class_name, $method_name)) {
      throw new Exception('method not found');
    }
    $this->checkAuth();
    return call_user_func_array(array($this->class_name, $method_name), $arguments);     
  }
  
  protected function checkAuth() {
    if(!$this->authenticated) {
      HTML_Output::error(403);
      throw new SoapFault("authenticate", "User not valid.");
    }
    return true;
  }
  
}




//IMPLEMENTATION: 
if (isset($_REQUEST['choice'])) {
  $rps = new rps();
  $rps->playGame($_REQUEST['choice']);
}
else if (isset($_REQUEST['format']) && $_REQUEST['format'] != "soap") {
  $rps = new rps();
  $rps->playGame();
}
else {
  $server = new SoapServer(null, array('uri' => "urn://bcmoney-mobiletv.com/api/soap/RockPaperScissors"));
  $server->setClass("rps");
  $server->handle();
}

?>
