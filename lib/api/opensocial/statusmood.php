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
    // Default to myspace because this is a myspace specific endpoint.
    if(!isset($_REQUEST["test"]))
         $_REQUEST["test"] = 'myspace';
         
	require_once "__init__.php";
	
	if ($osapi) {
	  if ($strictMode) {
	    $osapi->setStrictMode($strictMode);
	  }
	  
	  // Start a batch so that many requests may be made at once.
	  $batch = $osapi->newBatch();
	  
	  $params = array('fields'=>'recentComments');
	  // Fetch the status mood MySpace specific.
	  $batch->add($osapi->statusmood->get($params), 'get_status_mood');
	  
      // Fetch the status mood history self
      $batch->add($osapi->statusmood->getHistory(), 'get_history_self');
	  
      $params = array('groupId'=>'@friends');
      
      // Fetch the status mood history friends
      $batch->add($osapi->statusmood->getHistory($params), 'get_history_friends');
      
      
      // Fetch the status mood history specific friend
      $params = array('groupId'=>'@friends', 'friendId'=>'myspace.com.person.63129100');
      $batch->add($osapi->statusmood->getHistory($params), 'get_history_friend');
      
      
	  // Set the status mood MySpace specific.
	  $params = array( 'userId'=>'@me',
	                   'groupId'=>'@self',
                	   'statusMood'=>
                	      array(
                	      	'moodName' =>'excited',
                	      	'status' => 'Working on PHP SDK'
                	      )
	   );
	  //$batch->add($osapi->statusmood->update($params), 'set_status_mood');
	  
	  // Get one supported mood
	  $params = array( 'userId'=>'@me', 
	                   'groupId'=>'@supportedMood', 
	                   'moodId'=>90
	  );
      $batch->add($osapi->statusmood->getSupportedMoods($params), 'supportedMood');
      
	  // Get all supported moods
	  $params = array( 'userId'=>$userId, 
                       'groupId'=>'@supportedMood'
      );
	  $batch->add($osapi->statusmood->getSupportedMoods($params), 'supportedMoods');
	  
	  
	  // Send the batch request.
	  $result = $batch->execute();
	?>
	
	<h1>StatusMood API Examples</h1>
	<h2>Request:</h2>
	<p><b>NOTE: This entire endpoint is a myspace extension to OpenSocial v0.9.</b><br />
	This sample fetched statusmood self and friends. Requested statusmood history for self, friends, and a specific friend.
	Then it can update statusmood (disabled by default). It also requests a specific moods details, 
	then request all supported moods.</p>
	<?php
	
        require_once('response_block.php');
	}
?>