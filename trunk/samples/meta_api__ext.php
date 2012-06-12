<?php
# Sample to demonstrate usage of the META ext resource.
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
  $item1 = $rass->createItem("/test/content/", "myfile_one.mp4", LOCAL_PATH, True);
  $item2 = $rass->createItem("/test/content/", "myfile_two.mp4", LOCAL_PATH, True);
  
  # create content instance for item1
  $f = array();
  $f[] = new FileObj($item1->entry->content->params->path);
  $m = array();
  $m[] = new MetaObj("Title", "rss", "My first file", "en");
  $m[] = new MetaObj("Description", "rss", "Description of my first file", "en");
  $content_obj1 = new MetaContent("myfile_one", $f, null, $m);
  $content = $meta->createContent($content_obj1->to_entry());
  $content_obj1->from_entry($content);
  echo "\nCreated new content instance with name = " . $content_obj1->name . "\n";
  
  # create content instance for item2
  $f = array();
  $f[] = new FileObj($item2->entry->content->params->path, "video");
  $m = array();
  $m[] = new MetaObj("Title", "rss", "My second file", "en");
  $m[] = new MetaObj("Description", "rss", "Description of my second file", "en");
  $content_obj2 = new MetaContent("myfile_two", $f, null, $m);
  $content = $meta->createContent($content_obj2->to_entry());
  $content_obj2->from_entry($content);
  echo "\nCreated new content instance with name = " . $content_obj2->name . "\n";
  
  echo "\nGetting json list... \n";
  $json_list = $meta->getExtJson();
  print_r($json_list);
  
  echo "\nGetting atom list... \n";
  $atom_list = $meta->getExtAtom();
  print_r($atom_list);
  
  echo "\nGetting mrss list... \n";
  $mrss_list = $meta->getExtMrss();
  print_r($mrss_list);
  
  # delete content instance (this will not delete the file!)
  $meta->deleteContent($content_obj1->name);
  echo "\nDeleted content with name: " . $content_obj1->name . "\n";
  $meta->deleteContent($content_obj2->name);
  echo "\nDeleted content with name: " . $content_obj2->name . "\n";
    
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>