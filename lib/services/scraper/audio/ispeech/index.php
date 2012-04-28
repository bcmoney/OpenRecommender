<?php
  
  require_once "iSpeech.class.php"; 
    
  /**
   * iSpeech PHP Script (2011-12-07), version 0.3 (beta)
   *
   * Requires the cURL PHP extension
   * Designed for cloud-based speech synthesis and speech recognition.
   *
   * For more information, visit: http://www.ispeech.org/api
   * Please update the API key from, "developerdemokeydeveloperdemokey" to your API Key
   * Keys available at:
   *   http://www.ispeech.org/developers
   */
  
  $action = (!empty($_REQUEST['action'])) ? $_REQUEST['action'] : 'synthesis';  
  $text = (!empty($_REQUEST['text'])) ? $_REQUEST['text'] : "Hello";
  $filename = (!empty($_REQUEST['filename'])) ? $_REQUEST['filename'] : "testing.wav"; //filename to save any TTS to (or file to process if Recognize or Commandlist chosen)
  
  $result = null;
  /* Speech Recognition Command List demo - recognize audio using speech recognition from a list of commmands */
  if (strtolower($action) == 'commandlist') {
    $SpeechRecognizer = new SpeechRecognizer();
    $SpeechRecognizer->setParameter("server", "http://api.ispeech.org/api/rest");
    $SpeechRecognizer->setParameter("apikey", "developerdemokeydeveloperdemokey");
    $SpeechRecognizer->setParameter("freeform", "0");
    $SpeechRecognizer->setParameter("content-type", "wav");
    $SpeechRecognizer->setParameter("language", "en-US");
    $SpeechRecognizer->setParameter("output", "json");
    //The recognizer will return yes, no, or nothing
    $SpeechRecognizer->setParameter("alias", "command1|YESNO");
    $SpeechRecognizer->setParameter("YESNO", "yes|no");
    $SpeechRecognizer->setParameter("command1", "%YESNO%");
    //send raw audio of user's command for Command Recognition    
    $SpeechRecognizer->setParameter("audio", base64_encode(file_get_contents($filename)));
    $result = $SpeechRecognizer->makeRequest();
  }
  /* Speech Recognition demo - recognize audio using speech recognition */  
  else if (strtolower($action) == 'recognize') {
    $SpeechRecognizer = new SpeechRecognizer();
    $SpeechRecognizer->setParameter("server", "http://api.ispeech.org/api/rest");
    $SpeechRecognizer->setParameter("apikey", "developerdemokeydeveloperdemokey");
    $SpeechRecognizer->setParameter("freeform", "3");
    $SpeechRecognizer->setParameter("content-type", "wav");
    $SpeechRecognizer->setParameter("language", "en-US");
    $SpeechRecognizer->setParameter("output", "json");
    //send an Audio recording for Speech Recognition
    $SpeechRecognizer->setParameter("audio", base64_encode(file_get_contents($filename)));
    $result = $SpeechRecognizer->makeRequest();
  }  
  /* Speech Synthesis demo - Text To Speech (TTS) generates audio using speech synthesis (check your directory to find the audio file) */
  else {    
    $SpeechSynthesizer = new SpeechSynthesizer();
    $SpeechSynthesizer->setParameter("server", "http://api.ispeech.org/api/rest");
    $SpeechSynthesizer->setParameter("apikey", "developerdemokeydeveloperdemokey"); // 38a2f9160fdb5acaa03bdf161dd89e4e
    $SpeechSynthesizer->setParameter("text", $text);
    $SpeechSynthesizer->setParameter("format", "wav");
    $SpeechSynthesizer->setParameter("voice", "usenglishfemale");
    $SpeechSynthesizer->setParameter("output", "rest");
    $result = $SpeechSynthesizer->makeRequest();
  }
  
?>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>iSpeech API</title>
</head>
<body>
  <div>
    <form method="post">
      <fieldset>
        <legend>iSpeech API</legend>
        <select id="action" name="action">
          <option value="synthesis" selected="selected">Text-To-Speech (TTS)</option>
          <option value="recognition">Speech Recognition (SR)</option>
          <option value="commandlist">Command List (IVR)</option>
        </select>
        <input type="text" id="text" name="text" />        
        <br/>
        <input type="file" id="file" name="file" title="Select a file to recognize audio on" />
        <br/>
        <br/>
        <input type="submit" id="process" name="process" value="Process" />
      </fieldset>
    </form>    
    <div class="audioplayback">
      <audio src="testing.wav" controls="controls">
      </audio>
    </div>
    <pre>
    <?php 
    if (is_array($result)) {
      print_r($result);
    }
    else {
      echo file_put_contents($filename, $result);
    }
    ?>
    </pre>
  </div>
</body>
</html>