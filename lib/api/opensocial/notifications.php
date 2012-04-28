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

  // Set the status mood MySpace specific.
    $mediaItem = new osapiMediaItem();
    $mediaItem->setField('uri', 'http://api.myspace.com/v1/users/63129100');
    
    $notification = new osapiNotification();
    $notification->setField('recipientIds', array('63129100'));
    $notification->setField('mediaItems', array($mediaItem));
    $notification->setTemplateParameter('content', 'Hi ${recipient}, here\'s a notification from ${canvasUrl}');
    
  $params = array('notification'=>$notification);
  
  $batch->add($osapi->notifications->create($params), 'send_notification');
  
  // Send the batch request.
  $result = $batch->execute();
?>

<h1>Notification API Examples</h1>
<h2>Request:</h2>
<p>This sample creates a notification(msypace specific)</p>
<?php

      require_once('response_block.php');
}
?>