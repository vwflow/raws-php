<?php
# Sample to demonstrate how to use the RASE user resources.
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

require_once 'raws_json/rase_service.php';

try {
  $rase = new RaseService(USER, PWD);

  echo "\n\nGet wowza app list:";
  $list = $rase->getWowappList();
  foreach($list->feed->entry as $e) {
    echo "\n\nWowapp with name = " . $e->content->params->name;
    echo "\n - description = " . $e->content->params->description; 
    echo "\n - stream_type = " . $e->content->params->stream_type; 
    echo "\n - stream_uri = " . $e->content->params->stream_uri; 
    echo "\n - master_uri = " . $e->content->params->master_uri; 
    echo "\n - broadcast_domain = " . $e->content->params->broadcast_domain; 
    echo "\n - broadcast_ip = " . $e->content->params->broadcast_ip; 
    echo "\n - broadcast_backup_domain = " . $e->content->params->broadcast_backup_domain; 
    echo "\n - broadcast_backup_ip = " . $e->content->params->broadcast_backup_ip; 
    echo "\n - secure_token = " . $e->content->params->secure_token; 
  }
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>