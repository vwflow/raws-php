<?php
# Sample to demonstrate usage of the RASS item resource.
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
define('PWD', "xxx"); # password of your Rambla user account
define('RASS_SERVER', 'rass.cdnXX.rambla.be'); # either 'rass.cdn01.rambla.be' or 'rass.cdn02.rambla.be' (depending on the subCDN on which your account is located)
define('LOCAL_PATH', '/path/to/local/file.mp4'); # add path to local (video) file, to be uploaded to the CDN
define('DOWNLOAD_PATH', '/path/to/download/file.mp4'; # add path to local (video) file, to be uploaded to the CDN

require_once 'raws_json/json_client.php';
require_once 'raws_json/rass_service.php';

try {
  $rass = new RassService(USER, PWD, RASS_SERVER);

  # upload file to CDN
  $item = $rass->createItem("/test/sample/", "item.mp4", LOCAL_PATH, True);
  echo "\nCreated item: " . $item->entry->id . "\n";
  echo "\nItem has relative path = " . $item->entry->content->params->path . "\n";

  # download file via RASS
  $local_path = $rass->getItem($item->entry->content->params->path, DOWNLOAD_PATH);
  echo "\nDownloaded item to location: " . $local_path . "\n";
  
  # delete file from cdn
  $rass->deleteItem($item->entry->content->params->path);
  echo "\nDeleted item with path = " . $item->entry->content->params->path . "\n";

}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>