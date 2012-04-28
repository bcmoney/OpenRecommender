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

  // Request the activities of the current user.
  $user_params = array(
      'userId' => $userId,
      'groupId' => '@self',
      'count' => 10
  );
  $batch->add($osapi->activities->get($user_params), 'userActivities');

  // Get the current user's friends' activities.
  $friend_params = array(
      'userId' => $userId,
      'groupId' => '@friends',
      'count' => 5
  );
  $batch->add($osapi->activities->get($friend_params), 'friendActivities');

  // Create an activity (you could add osapiMediaItems to this btw)
  $activity = new osapiActivity();
  
  // Myspace requires some addtional things to be set.
  if(isset($_REQUEST["test"]) && $_REQUEST["test"] == 'myspace') {
    $msParameters = array();
    $msParameters[] = array("key"=>"content", "value"=>"Hello there, this is my template parama content.");
    $msParameters[] = array("key"=>"service", "value"=>"PHP SDK Updated ". time());
    $activity->setField('templateParams', $msParameters);
    $activity->setField('titleId', 'Template_1');
  }else {
    $activity->setField('title', 'osapi test activity at ' . time());
    $activity->setField('body', 'osapi test activity body');
  }
  
  $create_params = array(
      'userId' => $userId,
      'groupId' => '@self',
      'activity' => $activity,
      'appId' => $appId
  );
  $batch->add($osapi->activities->create($create_params), 'createActivity');

  // supported fields
  $batch->add($osapi->activities->getSupportedFields(), 'supportedFields');

/* EXAMPLE: create a message
$batch->add($osapi->messages->create(array('userId' => $userId, 'groupId' => '@self', 'message' => new osapiMessage(array(1), 'test message by osapi', 'send at '.strftime('%X')))), 'createMessage');
*/

  // Send the batch request.
  $result = $batch->execute();
?>

<h1>Activities Example</h1>

<h2>Request:</h2>
<p>This sample fetched the activities for the current user and their
  friends.  Then the sample attempts to create a message.</p>

<?php
  require_once('response_block.php');
}
