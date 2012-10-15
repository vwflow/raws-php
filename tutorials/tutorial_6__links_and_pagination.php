<?php
# Sample script for RAWS tutorial 6: see http://rambla.eu/tutorial-6-handling-links-and-pagination-php for more info.
#
# Copyright (C) 2012 rambla.eu

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
  
  ## TUTORIAL PRE-CONFIG: FIRST CREATE DIRECTORY + UPLOAD FILES TO HAVE SOME TEST-DATA

  # Create a "tutorial6" directory under the root-directory
  $item = null;
  $dir = $rass->createDir("tutorial6", True);
  echo "Created directory with path: " . $dir->entry->content->params->path . "\n";
  $ctr = 1;
  # Upload the local file 15 times as 'testXX.mp4' to the "tutorial6" directory (creating some test data)
  while ($ctr < 16) {
    $item = $rass->createItem($dir->entry->content->params->path, "test$ctr.mp4", LOCAL_FILE);
    echo "Created file with path: " . $item->entry->content->params->path . "\n";
    $ctr += 1;
  }
  
  
  ## GETTING LINK ELEMENTS FOR AN ENTRY
  
  # Directly getting at an item entry's link elements
  echo "\nGetting all link elements for entry with path: " . $item->entry->content->params->path . "\n";
  foreach($item->entry->link as $link) {
    echo "\nType of relation: " . $link->rel;
    echo "\nLink URI: " . $link->href;
    echo "\nExpected type: " . $link->type . "\n";
  }
  
  # Using a helper method to get to an entry's enclosure URL
  echo "\nGetting enclosure link (= download location of the file on the CDN) via helper method: " . $rass->getEnclosureLink($item) . "\n";
  
  
  ## GETTING LINK ELEMENTS FOR A FEED  
  
  # Retrieve a list of the files inside the 'tutorial6' sub-directory
  $dir_list = $rass->getDirList($dir->entry->content->params->path, "kind=file");
  
  # Directly getting at the item entry's link elements
  echo "\nGetting all link elements for feed with ID: " . $dir_list->feed->id . "\n";
  foreach($dir_list->feed->link as $link) {
    echo "\nType of relation: " . $link->rel;
    echo "\nLink URI: " . $link->href;
    echo "\nExpected type: " . $link->type . "\n";
  }
  
  
  ## USING PAGINATION (via helper functions)
  
  # Since we've uploaded 15 files, we can retrieve partial results by suggesting to RASS that we only want 10 results in a single response
  $dir_list = $rass->getDirList($dir->entry->content->params->path, "kind=file;paginate_by=10");
  
  # since the request only contains 10 entries, there should be a 'next' + 'last' link inside the feed element
  echo "\nNext link URI: " . $rass->getNextLink($dir_list) . "\n";
  echo "Last link URI: " . $rass->getLastLink($dir_list) . "\n";
  
  # There's also a helper function to directly get at the 'next' feed (takes the original feed as argument)
  $next_list = $rass->getNextList($dir_list);
  echo "\nThis feed contains all remaining entries => new call to getNextLink() will return empty string: " . $rass->getNextLink($next_list) . "\n";
  

  ## PAGINATION BEST PRACTICES

  # if you want to retrieve all results from a resource that supports pagination, you might want to use a while loop
  echo "\nGetting all files under " . $dir->entry->content->params->path . " using pagination:\n";
  $dir_list = $rass->getDirList($dir->entry->content->params->path, 'kind=file;paginate_by=10');
  while($dir_list)
  {
    foreach ($dir_list->feed->entry as $entry) {
      # process your entries here..
      echo "\nFound entry with path = " . $entry->content->params->path;
    }
    # get next feed, by sending a new request to the next link inside this page
    $dir_list = $rass->getNextList($dir_list);
    if ($dir_list) {
      echo "\nGetting next page..";
    }
  }


  ## TUTORIAL CLEANUP

  # cleanup the tutorial6 dir
  $rass->deleteDir($dir->entry->content->params->path, True);
  echo "\nDeleted dir: " . $dir->entry->content->params->path . "\n";
    
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}
