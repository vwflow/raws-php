<?php
# Sample to demonstrate how to use the RATS src resource.
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
define('LOCAL_PATH', '/path/to/local/video/file'); # add path to local (video) file, to be uploaded to the CDN

require_once 'raws_json/json_client.php';
require_once 'raws_json/rats_service.php';

try {
  $rats = new RatsService(USER, PWD);

  # upload src file
  $src = $rats->createSrc("test_rats_api_src", LOCAL_PATH);
  echo "\nUploaded src: " . $src->entry->id;
  echo "\nSrc has filename = " . $src->entry->content->params->filename;
  
  # retrieve a single src instance based on the filename
  $src = $rats->getSrcInstance($src->entry->content->params->filename);
  echo "\nRetrieved src with filename = " . $src->entry->content->params->filename;

  echo "\nGetting src list:";
  $src_list = $rats->getSrcList();
  foreach($src_list->feed->entry as $o) {
    echo "\n Src has filename = " . $o->content->params->filename;
  }
  
  # delete src
  $rats->deleteSrc($src->entry->content->params->filename);
  echo "\nDeleted src with filename = " . $src->entry->content->params->filename;


}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>