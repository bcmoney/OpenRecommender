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

  $batch->add($osapi->system->listMethods(), 'methods');

  // Send the batch request.
  $result = $batch->execute();

  ?>

<h1>system.listMethods Example</h1>

<h2>Request:</h2>
<p>This sample fetched the methods that the container supports (notice:
this only works on OpenSocial &gt;= 0.9 based sites which also support json-rpc)</p>

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
      echo htmlentities(print_r($result_item, true));
      echo "</pre>";
    }
  }
}
