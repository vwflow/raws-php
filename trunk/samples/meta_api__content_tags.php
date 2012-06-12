<?php
# Sample to demonstrate usage of the META content resource (tags).
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
  $meta = new MetaService(USER, PWD);
  $rass = new RassService(USER, PWD, RASS_SERVER);

  # upload file to CDN (RASS PUT item)
  $item = $rass->createItem("/test/content/", "big_buck_bunny.mp4", LOCAL_PATH, True);
  
  # create content instance without metadata
  $content_obj = new MetaContent("big_buck_bunny");
  $content_obj->add_file_obj($item->entry->content->params->path);
  $content = $meta->createContent($content_obj->to_entry());
  echo "\nCreated new content instance with name = " . $content->entry->content->params->name . "\n";

  # add tags to the content instance
  $content_obj->from_entry($content);
  $content_obj->set_tags(array("bunny", "animation", "Blender"));
  $content = $meta->updateContent($content_obj->to_entry());
  echo "\nAdded tags to content instance with name = " . $content_obj->name . "\n";
  
  # get content instance
  $content = $meta->getContentInstance($content_obj->name);
  $content_obj->from_entry($content);
  echo "\nRetrieved content instance with id = " . $content->entry->id . " and tags: ";
  foreach ($content_obj->get_tags() as $t) {
    echo "\n* " . $t;
  }
  
  # get content feed
  echo "\n\nGetting content list...";
  $feed = $meta->getContentList("tag=animation");
  foreach($feed->feed->entry as $content_entry) {
    echo "\n* retrieved content: " . $content_entry->id . " with name = " . $content_entry->content->params->name;
  }
  echo "\n... finished retrieving content instances.\n";
  
  # delete content instance + file from CDN
  $meta->deleteContent($content_obj->name);
  echo "\nDeleted content with name: " . $content_obj->name . "\n";
    
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>