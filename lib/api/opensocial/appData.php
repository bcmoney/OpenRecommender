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

require_once "__init__.php";

if ($osapi) {
  if ($strictMode) {
    $osapi->setStrictMode($strictMode);
  }
  
  // Start a batch so that many requests may be made at once.
  $batch = $osapi->newBatch();
  
  // Get the current user's app data
  $app_data_self_params = array(
      'userId' => $userId, 
      'groupId' => '@self', 
      'appId' => $appId
  );
  $batch->add($osapi->appData->get($app_data_self_params), 'appdataSelf');

  // Get the app data for the user's friends
  $app_data_friends_params = array(
      'userId' => $userId, 
      'groupId' => '@friends', 
      'appId' => $appId
  );
  $batch->add($osapi->appData->get($app_data_friends_params), 'appdataFriends');
  
  // Create some app data for the current user 
  $create_params = array(
      'userId' => '@me', 
      'groupId' => '@self', 
      'appId' => $appId,
      'data' => array(
          'osapiFoo1' => 'bar1', 
          'osapiFoo2' => 'baz1', 
          'osapiFoo3' => 'bat1'
      )
  );
  $batch->add($osapi->appData->create($create_params), 'createAppData');
  
  // Update app data for the current user
  $update_params = array(
      'userId' => '@me', 
      'groupId' => '@self', 
      'appId' => $appId,
      'data' => array(
          'osapiFoo1' => date("c")
      )
  );
  $batch->add($osapi->appData->update($update_params), 'updateAppData');
  
  // Get the app data again to show the updated value
  $get_params = array(
      'userId' => '@me', 
      'groupId' => '@self', 
      'appId' => $appId,
      'fields' => array(
          'osapiFoo1', 
          'osapiFoo2', 
          'osapiFoo3'
      )
  );
  $batch->add($osapi->appData->get($get_params), 'getAppData');
  
  // Delete the keys we created in the previous examples
  $delete_params = array(
      'userId' => '@me', 
      'groupId' => '@self',
      'appId' => $appId, 
      'fields' => array(
          'osapiFoo1', 
          'osapiFoo2', 
          'osapiFoo3'
      )
  );
  $batch->add($osapi->appData->delete($delete_params), 'deleteAppData');

  // Send the batch request.
  $result = $batch->execute();
?>

<h1>App Data Example</h1>

<h2>Request:</h2>
<p>This sample fetched all of the app data for the current user and their 
  friends.  Then it set app data for the keys <em>osapiFoo1</em>, 
  <em>osapiFoo2</em>, and <em>osapiFoo3</em>, updated the key 
  <em>osapiFoo1</em> with a new value, fetched all three fields again, and
  then deleted the three keys in the same batch request.</p>

<?php
  require_once('response_block.php');
}
