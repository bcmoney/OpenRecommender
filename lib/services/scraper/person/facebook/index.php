<?php

require 'src/facebook.php';

/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => '105774552787347',
  'secret' => '4003a7259f01732c7805bb2326f2f866'
));
$user = $facebook->getUser(); // Get User ID

$friend_list = null;
$user_info = '';
$user_friends = ''; 
// We may or may not have this data based on whether the user is logged in. If we have a $user id here, it means we know the user is logged into Facebook, but we don't know if the access token is valid. An access token is invalid if the user logged out of Facebook.
if ($user) {
  try {
    $user_profile = $facebook->api('/me'); // Proceed knowing you have a logged in user who's authenticated.
    $logoutUrl = $facebook->getLogoutUrl(); // Login URL will be needed for current user state.
    $login = '<a href="'.$logoutUrl.'">Logout</a>';
    $user_info = '<h3>You</h3><img id="profile_pic" src="https://graph.facebook.com/'.$user_profile['username'].'/picture" /><strong>Name:</strong> <span id="firstname">'.$user_profile['first_name'].'</span> <span id="lastname">'.$user_profile['last_name'].'</span><br/><strong>Email:</strong> <span id="email">'.$user_profile['email'].'</span><br/><span id="username">'.$user.'</span><br/><span id="website">'.$user_profile['website'].'</span>';
      try {
        $fql   = 'select name,username,pic_square,uid from user where uid in (select uid1 from friend where uid2="'.$user.'")';
        $param = array(
          'method'    => 'fql.query',
          'query'     => $fql,
          'callback'  => ''
        );
        $friend_list = $facebook->api($param);
      } catch(Exception $o) {
          error_log($o);
      }
      $user_friends = '<div><ul class="friendlist">';
      $i=1;    
      foreach($friend_list as $rec) {
        $user_friends .= '<li class="friend"><a href="https://www.facebook.com/profile.php?id='.$rec['uid'].'" title="'.$rec['name'].'" rel="'.$rec['username'].'" target="_blank"><img alt="'.$i.'" src="'.$rec['pic_square'].'"></a></li>';
        $i++;
      }
      $user_friends .= '</ul></div><br class="clear"/>Displaying "'.$i.'" friends | <a id="foaf-button" href="#foaf">Generate FOAF</a>';    
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
} else {
  $loginUrl = $facebook->getLoginUrl(); // Logout URL will be needed for current user state.
  $user_profile = $facebook->api('/bcmoney'); // This call will always work since we are fetching MY public data.
  $login = '<div>Login using OAuth 2.0 via PHP SDK: <a href="'.$loginUrl.'">Login with Facebook</a></div>';
  $user_info = '<strong><em>You are not Connected.</em></strong><h3>Public profile of developer '.$user_profile['name'].' instead.</h3><img src="https://graph.facebook.com/'.$user_profile['username'].'/picture" />'; 
}
?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>SkipSearch - Facebook GraphAPI-x</title>    
<!-- STYLE -->
    <style type="text/css">
      body { font-family:'Lucida Grande','Trebuchet MS',Verdana,Arial,sans-serif; font-size:12px }
      h1 a { text-decoration:none; color:#3b5998 }
      h1 a:hover { text-decoration:underline }
      div { width:100%; height:auto }
      form{	width:500px; margin:190px auto 0; text-align:center }
      textarea{	resize:none; width:400px; height:280px;	margin-bottom:20px;	font:14px/1 'Segoe UI',Arial,sans-serif; color:#333;	padding:4px; overflow:auto }
      .blueButton, .greenButton {	background:url('buttons.png') no-repeat;	text-shadow:1px 1px 1px #277c9b; color:#fff !important;	width:99px;	height:38px; border:none;	text-decoration:none;	display:inline-block;	font-size:16px; line-height:32px; text-align:center; margin:0 4px }
      .greenButton { background:url('../img/buttons.png') no-repeat right top; text-shadow:1px 1px 1px #498917 }
      .blueButton:hover, .greenButton:hover{ background-position:left bottom; text-decoration:none !important }
      .greenButton:hover {	background-position:right bottom }
      .blueButton:active, .greenButton:active { position:relative; bottom:-1px }
      .friendlist { list-style-type:none; padding:0px }
      .friend { float:left; list_style:none; margin:5px }
      .foaf-button { background:#090; color:#fff; font-weight:bold; font-size:1.2em }
      #foaf { display:none }
      .clear { clear:both }
    </style>
  </head>
  <body>
    <?php echo $login; ?>
    <!-- User Info -->
    <?php echo $user_info; ?>
    <!-- User's Friends -->
    <?php echo $user_friends; ?><br/>
    <div id="foaf">
        <form action="./" method="post">
            <textarea></textarea>
            <a href="#" class="blueButton" id="download">Download</a>
        </form>
    </div>
    <!-- DEBUG -->
    <h3>PHP Session</h3>
    <pre><?php print_r($_SESSION); ?></pre>
    <h3>Your User Object (/me)</h3>
    <pre><?php print_r($user_profile); ?></pre>
<!-- BEHAVIOR -->
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script src="jquery.generateFile.js"></script>  
    <script type="text/javascript">
    $(document).ready(function() {
      
      var username = $('#username').text();
                
      $("#foaf-button").on("click", function(e) {
          $("#foaf").show();
            var foafString = '<rdf:RDF xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:doap="http://usefulinc.com/ns/doap#" xmlns:foaf="http://xmlns.com/foaf/0.1/" xmlns:owl="http://www.w3.org/2002/07/owl#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:admin="http://bcmoney-mobiletv.com/">' + 
              '<foaf:PersonalProfileDocument rdf:about="'+username+'">'+ 
              '  <foaf:maker rdf:resource="#me"/>' + 
              '  <foaf:primaryTopic rdf:resource="#me"/>' + 
              '  <admin:generatorAgent rdf:resource="http://skipsearch.net"/>' + 
              '  <admin:errorReportsTo rdf:resource="mailto:bc@bcmoney-mobiletv.com"/>' + 
              '</foaf:PersonalProfileDocument>'+
              '<foaf:Person rdf:ID="me">' + 
              '  <foaf:name>'+$('#firstname').text()+' '+$('#lastname').text()+'</foaf:name>' + 
              '  <foaf:title>'+$('#gender').text().substring(0,1)+'</foaf:title>' + 
              '  <foaf:givenname>'+$('#firstname').text()+'</foaf:givenname>' + 
              '  <foaf:family_name>'+$('#lastname').text()+'</foaf:family_name>' + 
              '  <foaf:nick>'+username+'</foaf:nick>' + 
              '  <foaf:mbox>'+$('#email').text()+'</foaf:mbox>' + 
              '  <foaf:phone rdf:resource="'+$('#phone').text()+'"/>' + 
              '  <foaf:homepage rdf:resource="'+$('#website').text()+'"/>' + 
              '  <foaf:depiction rdf:resource="'+$('#profile_pic').src+'"/>' + 
              '  <foaf:interest rdf:resource="Getting off Facebook"/>' + 
              '<foaf:holdsAccount>' + 
              '  <foaf:OnlineAccount>' + 
              '    <foaf:accountServiceHomepage rdf:resource="http://facebook.com/"/>' + 
              '    <foaf:accountProfilePage rdf:resource="'+$('#account-link').attr('href')+'"/>' + 
              '    <foaf:accountName rdf:resource="'+username+'"/>' + 
              '  </foaf:OnlineAccount>' + 
              '</foaf:holdsAccount>';
          $('li.friend a').each(function(index) {          
            foafString += '  <foaf:knows>' + 
              '    <foaf:Person>' + 
              '    <foaf:name>'+$(this).attr('title')+'</foaf:name>' + 
              '    <foaf:nick>'+$(this).attr('rel')+'</foaf:nick>' + 
              '    <foaf:depiction rdf:resource="' + $(this).children(":first").attr('src') + '"/>' + 
              '    <foaf:homepage>' + $(this).attr('href') + '</foaf:homepage>' + 
              '    <rdfs:seeAlso rdf:resource="http://skipsearch.net/user/'+encodeURIComponent($(this).attr('title'))+'"/>' + 
              '    </foaf:Person>' + 
              '  </foaf:knows>';
          });
          foafString += '</foaf:Person>';
          $('#foaf textarea').text(foafString);
        }      
      );
      
      $('#download').on("click", function(e) {
        $.generateFile({
          filename	: username+'.xml',
          content		: $('textarea').val(),
          script		: 'download.php'
        });        
        e.preventDefault();
      });
      
    });
    </script>
  </body>
</html>