<?php
# Sample to demonstrate how to use the RATS transc resource.
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
define('DOWNLOAD_PATH', '/path/to/local/download/location'); # path to a file in which to store the transcoded file

require_once 'raws_json/json_client.php';
require_once 'raws_json/rats_service.php';

try {
  $rats = new RatsService(USER, PWD);
  $transc_filename = null;

  echo "\nGetting transc list:";
  $transc_list = $rats->getTranscList();
  foreach($transc_list->feed->entry as $o) {
    echo "\n Transc has filename = " . $o->content->params->filename;
    $transc_filename = $o->content->params->filename;
  }

  # retrieve a single transc instance based on the filename
  $transc = $rats->getTranscInstance($transc_filename);
  echo "\nRetrieved transc with filename = " . $transc->entry->content->params->filename;
  
  # retrieve a single transc instance based on the filename
  $downloaded_filepath = $rats->getTranscFile($transc_filename, DOWNLOAD_PATH);
  echo "\nDownloaded transc to location = " . $downloaded_filepath;
  
  # delete transc
  $rats->deleteTransc($transc_filename);
  echo "\nDeleted transc with filename = " . $transc->entry->content->params->filename;


}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>