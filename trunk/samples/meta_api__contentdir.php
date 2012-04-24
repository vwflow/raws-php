<?php
# Sample to demonstrate usage of the META contentdir resource.
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

require_once 'raws_json/meta.php';
require_once 'raws_json/json_client.php';
require_once 'raws_json/meta_service.php';
require_once 'raws_json/rass_service.php';

try {
  $rass = new RassService(USER, PWD, RASS_SERVER);
  $meta = new MetaService(USER, PWD);

  # upload file to CDN (RASS PUT item)
  $item1 = $rass->createItem("/test/content/", "myfile_with_meta.mp4", LOCAL_PATH, True);
  $item2 = $rass->createItem("/test/content/", "myfile_no_meta.mp4", LOCAL_PATH, True);
  
  # create content instance
  $f = array();
  $f[] = new FileObj($path = $item1->entry->attrs->path, $media_type = "video");
  $content = new MetaContent($name = "myfile_with_meta", $file_objs = $f);
  $content->tags = array("een", "twee", "drie");
  $content_obj = $meta->createContent($content->to_entry());
  $content->from_entry($content_obj);
  echo "\nCreated new content instance with name = " . $content->name . "\n";
  
  # get contentdir feed
  echo "\n\nGetting contentdir list...";
  $feed = $meta->getContentdirList("/");
  foreach($feed->feed->entry as $e) {
    if ($e->content->params->name) {
      echo "\n* retrieved content instance: " . $e->id . " with name = " . $e->content->params->name;
    }
    else {
      echo "\n* retrieved file without metadata, path = " . $e->content->file[0]->path;
    }
  }
  echo "\n... finished retrieving contentdir instances.\n";
  
  # delete content instance (this will not delete the file!)
  $meta->deleteContent($content->name);
  echo "\nDeleted content with name: " . $content->name . "\n";
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>