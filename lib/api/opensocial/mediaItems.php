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

// The id's for these tests are myspace specific
if(!isset($_REQUEST["test"]))
     $_REQUEST["test"] = 'myspace';

require_once "__init__.php";

if ($osapi) {
  if ($strictMode) {
    $osapi->setStrictMode($strictMode);
  }
  
  //$osapi->setStrictMode(true);
  // Start a batch so that many requests may be made at once.
  $batch = $osapi->newBatch();

  // supported fields
  $batch->add($osapi->mediaItems->getSupportedFields(), 'supported_fields');
  
  // Request the mediaItems for album
  $user_params = array(
      'userId' => $userId, 
      'groupId' => '@self', 
      'albumId' =>'myspace.com.album.81886',
      'count' => 2
  );
  $batch->add($osapi->mediaItems->get($user_params), 'get_mediaItems');
  
  // Fetch mediaItem by id
  $user_params = array(
      'userId' => $userId, 
      'groupId' => '@self',
  	  'fields'=>'albumId,ratings,tags,numcomments',
      'albumId' =>'myspace.com.album.81886',
  	  'mediaItemId'=>'myspace.com.mediaItem.image.646364'
  );
 $batch->add($osapi->mediaItems->get($user_params), 'get_mediaItem_for_id');
  
   // Upload mediaItem
  $data = file_get_contents('images.jpg');
  $user_params = array(
      'userId' => '@me', 
      'groupId' => '@self',
      'albumId' =>'myspace.com.album.81886',
      'type'=>'IMAGE',
      'mediaItem' => $data,
  	  'contentType'=> 'image/jpg'
  );
  // Commented out so everyone doesn't upload a new file on test. 
  $batch->add($osapi->mediaItems->uploadContent($user_params), 'upload_mediaItem');
  
  // Update mediaItem
  $mediaItem = new osapiMediaItem();
  $mediaItem->setField('title', 'title '.time());
  $mediaItem->setField('caption', 'caption '.time());
  $mediaItem->setField('description', 'description '.time());
  $user_params = array(
      'userId' => '@me', 
      'groupId' => '@self',
      'albumId' =>'myspace.com.album.81886',
  	  'mediaItemId'=>'myspace.com.mediaItem.image.646364',
      'mediaItem' => $mediaItem
  );
  $batch->add($osapi->mediaItems->update($user_params), 'update_mediaItem');
  
  $user_params = array(
    'userId' => $userId, 
    'groupId' => '@self', 
    'albumId' =>'@videos',
    'count' => 2
  );
 
  // Request videos (MySpace Specific)
  $batch->add($osapi->mediaItems->get($user_params), 'get_videos');
  
  // Send the batch request.
  $result = $batch->execute();
?>

<h1>mediaItems Example</h1>
<h2>Request:</h2>
<p>This sample fetched the mediaItems for the current user. Then gets mediaItem details for a specific mediaItem. Create a new mediaItem. Then update an existing mediaItem. Lastly we request videos. (MySpace specific)</p>

<?php
    require_once('response_block.php');
}
