<?php
# Sample to demonstrate how to use the RAMS user resources.
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

require_once 'raws_json/json_client.php';
require_once 'raws_json/rams_service.php';

try {
  $rams = new RamsService(USER, PWD);

  echo "\n\nGet total list using querystring:";
  $traffic = $rams->getTotalList("year=curr;month=curr;type=all");
  foreach($traffic->feed->entry as $e) {
    echo "\nTotal for type = " . $e->attrs->type; 
    echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - unique users = " . $e->content->params->unique; 
  }

  echo "\n\nGet traffic list for the root directory:";
  $traffic = $rams->getTrafficList("/", "type=http,rtmp");
  foreach($traffic->feed->entry as $e) {
    echo "\nTraffic for type = " . $e->attrs->type . ", kind = " . $e->attrs->kind  . " and path = " . $e->content->params->path; 
    echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - last_request = " . $e->content->params->last_request; 
  }
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>