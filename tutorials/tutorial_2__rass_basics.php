<?php
# Sample script for RAWS tutorial 2: see http://rambla.eu/raws-tutorial-2-basic-rass-operations-php-client for more info.
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

  # Creates a "tutorial2" directory below your root dir
  $dir = $rass->createDir("tutorial2", True);
  # Retrieve the entry's "path" param
  echo "Created directory with path: " . $dir->entry->content->params->path . "\n";
  # Note: the URL for accessing this new dir resource is available as the entry's id
  echo "New dir resource can be accessed at the URL: " . $dir->entry->id  . "\n";

  # Uploads the local file as 'taste.mp4' to the "tutorial2" directory on the CDN
  $item = $rass->createItem($dir->entry->content->params->path, "taste.mp4", LOCAL_FILE, True);
  # Retrieve the entry's "path" attribute
  echo "\nCreated file with path: " . $item->entry->content->params->path . "\n";
  # Retrieve the filename on the CDN
  echo "Filename: " . $item->entry->content->params->filename . "\n";
  # Get the location of the file on the CDN from the entry's 'enclosure' link
  echo "Public download location of the uploaded file: " . $rass->getEnclosureLink($item) . "\n";
  # Note: the URL for accessing this new dir resource is available as the entry's id
  echo "New dir resource can be accessed at the URL: " . $item->entry->id  . "\n";

  # PUT the same file for a second time => RASS will add a numerical suffix
  $item = $rass->createItem($dir->entry->content->params->path, "taste.mp4", LOCAL_FILE, True);
  # Retrieve the entry's "path" attribute
  echo "\nCreated file with path: " . $item->entry->content->params->path . "\n";
  # Retrieve the filename on the CDN
  echo "Filename: " . $item->entry->content->params->filename . "\n";
  # Get the location of the file on the CDN from the entry's 'enclosure' link
  echo "Public download location of the uploaded file: " . $rass->getEnclosureLink($item) . "\n";
  # Note: the URL for accessing this new dir resource is available as the entry's id
  echo "New dir resource can be accessed at the URL: " . $item->entry->id  . "\n";
  
  # DELETE file on the CDN
  $rass->deleteItem($item->entry->content->params->path);
  echo "\nDeleted file: " . $item->entry->content->params->path . "\n";
  
  # DELETE directory on the CDN. Set second argument to True to delete recursively.
  $rass->deleteDir($dir->entry->content->params->path, True);
  echo "\nDeleted dir: " . $dir->entry->content->params->path . "\n";
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}
