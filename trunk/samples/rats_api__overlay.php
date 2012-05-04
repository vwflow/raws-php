<?php
# Sample to demonstrate how to use the RATS overlay resource.
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
define('LOCAL_PATH', '/path/to/local/image/file'); # add path to local (image) file, to be uploaded to the CDN

require_once 'raws_json/rats_service.php';

try {
  $rats = new RatsService(USER, PWD);

  # upload overlay file
  $overlay = $rats->createOverlay("test_rats_api_overlay", LOCAL_PATH);
  echo "\nUploaded overlay: " . $overlay->entry->id;
  echo "\nOverlay has filename = " . $overlay->entry->content->params->filename . " and position = " . $overlay->entry->content->params->position;
  
  # update overlay position
  $overlay->entry->content->params->position = "10:10";
  $overlay = $rats->updateOverlay($overlay);
  echo "\nSet new overlay position: " . $overlay->entry->content->params->position;

  # retrieve a single overlay instance based on the ID
  $overlay = $rats->getOverlayInstance($overlay->entry->content->params->id);
  echo "\nRetrieved overlay with filename = " . $overlay->entry->content->params->filename . " and position = " . $overlay->entry->content->params->position;

  echo "\nGetting overlay list:";
  $overlay_list = $rats->getOverlayList();
  foreach($overlay_list->feed->entry as $o) {
    echo "\n Overlay has filename = " . $o->content->params->filename . " and position = " . $o->content->params->position;
  }


}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>