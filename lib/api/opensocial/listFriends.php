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

  // The fields we will be fetching.
  if (isset($_GET['test']) && $_GET['test'] == 'plaxo') {
    // plaxo is a PortableContacts end-point so doesn't know about the OpenSocial specific fields
    $profile_fields = array();
  } else {
    $profile_fields = array(
        'aboutMe',
        'displayName',
        'bodyType',
        'currentLocation',
        'drinker',
        'happiestWhen',
        'lookingFor'
    );
  }

  // The number of friends to fetch.
  $friend_count = 2;

  // Start a batch so that many requests may be made at once.
  $batch = $osapi->newBatch();

  // Fetch the current user.
  $self_request_params = array(
      'userId' => $userId,              // Person we are fetching.
      'groupId' => '@self',             // @self for one person.
      'fields' => $profile_fields       // Which profile fields to request.
  );
  $batch->add($osapi->people->get($self_request_params), 'self');

  // Fetch the friends of the user
  $friends_request_params = array(
      'userId' => $userId,              // Person whose friends we are fetching.
      'groupId' => '@friends',          // @friends for the Friends group.
      'fields' => $profile_fields,      // Which profile fields to request.
      'count' => $friend_count          // Max friends to fetch.
  );
  $batch->add($osapi->people->get($friends_request_params), 'friends');
  
  // Get supportedFields Request
  $batch->add($osapi->people->getSupportedFields(), 'supportedFields');
  
  // Send the batch request.
  $result = $batch->execute();

?>

<h1>List Friends Example</h1>

<h2>Request:</h2>
<p>This sample fetched the current viewer and
  <strong><?= $friend_count ?></strong> friends, asking for the fields:
  <em><?= implode($profile_fields, ", ") ?></em>. Request supported fields for this service.</p>

<?php

  // Demonstrate iterating over a response set, checking for an error,
  // and working with the result data.

  foreach ($result as $key => $result_item) {
    if ($result_item instanceof osapiError) {
      $code = $result_item->getErrorCode();
      $message = $result_item->getErrorMessage();
      echo "<h2>There was a <em>$code</em> error with the <em>$key</em> request:</h2>";
      echo "<pre>";
      echo htmlentities($message);
      echo "</pre>";
    } else {
      echo "<h2>Response for the <em>$key</em> request:</h2>";
      echo "<pre>";
      echo htmlentities(print_r($result_item, True));
      echo "</pre>";
    }
  }
}
