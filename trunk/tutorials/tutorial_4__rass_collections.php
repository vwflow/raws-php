<?php
# Sample script for RAWS tutorial 4: see http://rambla.eu/raws-tutorial-4-getting-collections-php-client for more info.
#
# Copyright (C) 2010 rambla.be

# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#      http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
require_once 'raws_json/rass_service.php';

# Provide the path to a local testfile, that can be uploaded via RASS
define('LOCAL_FILE', '/path/to/local/file'); # add path to local (video) file, to be uploaded to the CDN

# Provide your own RASS credentials here
define('USER', 'xxx'); # your user account name
define('PWD', 'xxx'); # your user account pwd
define('RASS_SERVER', 'rass.cdn0x.rambla.be'); # either 'rass.cdn01.rambla.be' or 'rass.cdn02.rambla.be' (depending on the subCDN on which your account is located)

# all RASS API methods (except HEAD) will raise an exception if they don't return an HTTP SUCCESS CODE (200,201,204)
try {
  # Instantiate an object that manages the RASS connection, passing it your login credentials and the base service uri
  $rass = new RassService(USER, PWD, RASS_SERVER);

  # Upload the local file as 'testX.mp4' to the CDN (= account's root-directory)
  $items = array();
  $ctr = 1;
  while ($ctr < 3) {
    $items[] = $item = $rass->createItem("/", "test$ctr.mp4", LOCAL_FILE);
    echo "Created file with path: " . $item->entry->content->params->path . "\n";
    $ctr += 1;
  }
  
  # Create a "bucks" directory under the root-directory
  $dir = $rass->createDir("bucks", True);
  echo "Created directory with path: " . $dir->entry->content->params->path . "\n";
  $ctr = 1;
  # Upload the local file as 'bunnyX.mp4' to the "bucks" directory
  while ($ctr < 3) {
    $item = $rass->createItem($dir->entry->content->params->path, "bunny$ctr.mp4", LOCAL_FILE);
    echo "Created file with path: " . $item->entry->content->params->path . "\n";
    $ctr += 1;
  }
  
  # Retrieve a list of the files inside our root-directory
  echo "\nGetting files located under the root-directory:\n";
  $dir_feed = $rass->getDirList("/", "kind=file");
  # Walk through the list of entries (entry == file)
  foreach($dir_feed->feed->entry as $entry) {
    # Retrieve the entry element's "kind" and "path" attributes
    echo "\nFound " . $entry->content->params->kind . " entry with path = " . $entry->content->params->path . "\n";
    # Retrieve the value of some file properties
    echo "Filename: " . $entry->content->params->filename . "\n";
    echo "Filesize: " . $entry->content->params->size . "\n";
    # Retrieve the public URL of the file (for download by end-users from the CDN)
    echo "Download URL: " . $rass->getEnclosureLink($entry) . "\n";
    # Retrieve URL at which we can access the item resource (= wraps a file) using RASS
    echo "Entry ID: " . $entry->id . "\n";
  }
  
  # Retrieve a list of the files inside the 'bucks' sub-directory
  echo "\nGetting files located under 'bucks' sub-directory:\n";
  $dir_feed = $rass->getDirList("/bucks", "kind=file");
  foreach ($dir_feed->feed->entry as $entry) {
    echo "\nFound " . $entry->content->params->kind . " entry with path = " .$entry->content->params->path . "\n";
    echo "Filename: " . $entry->content->params->filename . "\n";
    echo "Filesize: " . $entry->content->params->size . "\n";
    echo "Download URL: " . $rass->getEnclosureLink($entry) . "\n";
    echo "Entry ID: " . $entry->id . "\n";
  }

  # Retrieve a list of sub-directories of our root directory
  echo "\nGetting sub-directories of the root-direcory:\n";
  $dir_feed = $rass->getDirList("/", "kind=dir");
  foreach ($dir_feed->feed->entry as $entry) {
    echo "\nFound " . $entry->content->params->kind . " entry with path = " . $entry->content->params->path . "\n";
    echo "Dir name: " . $entry->content->params->name . "\n";
    echo "Dir size: " . $entry->content->params->size . "\n";
    echo "Entry ID: " . $entry->id . "\n";
  }
  
  # cleanup the $items under the root dir
  foreach ($items as $item) {
    $rass->deleteItem($item->entry->content->params->path);
    echo "Deleted file: " . $item->entry->content->params->path . "\n";
  }
  # cleanup the bucks dir
  $rass->deleteDir($dir->entry->content->params->path, True);
  echo "Deleted dir: " . $dir->entry->content->params->path . "\n";
    
}
catch(Zend_Gdata_App_Exception $e) {
    # Report the exception to the user
    echo "\nCaught exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    # get the HTTP status code
    echo "HTTP Status Code: " . $e->getResponse()->getStatus() . "\n";
    echo "Response Body with exception details: " . $e->getResponse()->getBody() . "\n";
    # get the raws:error elements
    $rawsResponse = Raws::parseRawsResponse($e->getResponse());
    echo "Raws Code: " . $rawsResponse->getCode() . "\n";
    echo "Raws Message: " . $rawsResponse->getMsg() . "\n";
    $reasons = $rawsResponse->getReasons();
    foreach ($reasons as $reason) {
      echo "Raws Reason: " . $reason . "\n";
    }
    $details = $rawsResponse->getDetails();
    foreach ($details as $key => $value) {
      echo "Raws Detail: " . $key . " -> " . $value . "\n";
    }
}
catch (Zend_Exception $e) {
    echo "Caught exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
}
