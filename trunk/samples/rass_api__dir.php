<?php
# Sample to demonstrate usage of the RASS dir resource.
#
# Copyright (C) 2012 rambla.eu
#
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

# To run this sample, define these variables first
define('USER', "xxx"); # name of your Rambla user account
define('PWD', "xxx;;"); # password of your Rambla user account
define('RASS_SERVER', 'rass.cdnXX.rambla.be'); # either 'rass.cdn01.rambla.be' or 'rass.cdn02.rambla.be' (depending on the subCDN on which your account is located)

require_once 'raws_json/json_client.php';
require_once 'raws_json/rass_service.php';

try {
  $rass = new RassService(USER, PWD, RASS_SERVER);

  # create dir on CDN
  $dir = $rass->createDir("/test/sample/dir", True);
  echo "\nCreated dir: " . $dir->entry->id . "\n";

  # retrieve feed of dirs
  echo "\nGetting dir list...";
  $feed = $rass->getDirList("/test/");
  foreach($feed->feed->entry as $e) {
    echo "\n* retrieved dir: " . $e->id . " with size = " . $e->content->params->size;
  }
  echo "\n... finished retrieving dir instances.\n";
  
  # delete file from cdn
  $rass->deleteDir($dir->entry->attrs->path);
  echo "\nDeleted dir with path = " . $dir->entry->attrs->path . "\n";

}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>