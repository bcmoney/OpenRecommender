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
  
  // Start a batch so that many requests may be made at once.
  $batch = $osapi->newBatch();
	
  $batch->add($osapi->albums->getSupportedFields(), 'supported_fields');
  
  // Request the albums of the current user.
  $user_params = array(
      'userId' => '@me', 
      'groupId' => '@self', 
      'count' => 2,
      'fields'=>'@all'
  );
  $batch->add($osapi->albums->get($user_params), 'get_albums');
  
  // Fetch album by id
  $user_params = array(
      'userId' => '@me', 
      'groupId' => '@self', 
      'albumId' => 'myspace.com.album.81886',
  	  'fields'=>'@all'
  );
  $batch->add($osapi->albums->get($user_params), 'get_album_for_id');
  
  // Create album
  $album = new osapiAlbum();
  $album->setField('caption', 'new album caption');
  $album->setField('description', 'new album description');
  $user_params = array(
      'userId' => '@me', 
      'groupId' => '@self', 
      'album' => $album
  );
  // Commented out so that everyone in the world doesn't creat an album =).
  //$batch->add($osapi->albums->create($user_params), 'create_album');
  
  // Update album
  $album = new osapiAlbum();
  $album->setField('caption', 'caption '.time());
  $album->setField('description', 'description '.time());
  $user_params = array(
      'userId' => '@me', 
      'groupId' => '@self', 
      'album' => $album,
  	  'albumId'=>'myspace.com.album.81886'
  );
  $batch->add($osapi->albums->update($user_params), 'update_album');
  
  // Send the batch request.
  $result = $batch->execute();
?>

<h1>Albums Example</h1>
<h2>Request:</h2>
<p>This sample fetched supported fields. The albums for the current user. Then gets album details for a specific album. Create a new album. Then update an existing album.</p>

<?php
    require_once('response_block.php');
}
