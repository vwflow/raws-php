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
  $list = $rams->getTotalList("year=curr;month=curr;type=all");
  foreach($list->feed->entry as $e) {
    echo "\nTotal for type = " . $e->content->params->type; 
    echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - unique users = " . $e->content->params->unique; 
  }

  echo "\n\nGet traffic list for the root directory:";
  $list = $rams->getTrafficList("/", "type=http,rtmp");
  foreach($list->feed->entry as $e) {
    echo "\nTraffic for type = " . $e->content->params->type . ", kind = " . $e->content->params->kind  . " and path = " . $e->content->params->path; 
    echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - last_request = " . $e->content->params->last_request; 
  }

  echo "\n\nGet top 50 domains in ascending order for the current month:";
  $list = $rams->getDomainList("order=asc");
  foreach($list->feed->entry as $e) {
    echo "\nTraffic for type = " . $e->content->params->type ." and domain = " . $e->content->params->domain; 
    echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - last_request = " . $e->content->params->last_modified; 
  }

  echo "\n\nGet top 50 city list for live HLS traffic in September 2012";
  $list = $rams->getCityList("year=2012;month=9;type=apple_http_live;order=desc");
  foreach($list->feed->entry as $e) {
    echo "\nTraffic for type = " . $e->content->params->type .", country = " . $e->content->params->country  . ", region = " . $e->content->params->region  . "and city = " . $e->content->params->city; 
    echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - last_request = " . $e->content->params->last_modified; 
  }

  echo "\n\nGet top 50 hosts for download and flash traffic for the current month:";
  $list = $rams->getHostList("order=desc;type=http,rtmp");
  foreach($list->feed->entry as $e) {
    echo "\nTraffic for type = " . $e->content->params->type ." and host = " . $e->content->params->host; 
    echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - last_request = " . $e->content->params->last_modified; 
  }

  echo "\n\nGet concurrent traffic on 14th of April 2012 between 20:00 and 23:30 CET, 100 entries at a time:";
  $list = $rams->getConcurrentV1List("order=desc;from=2012-04-14-20-00-00;until=2012-04-14-23-30-00;paginate_by=100");
  while ($list)
  {
    foreach($list->feed->entry as $e) {
      echo "\nTraffic for type = " . $e->content->params->type ." and datetime = " . $e->content->params->datetime; 
      echo " - number of concurrent viewers = " . $e->content->params->hits; 
    }
    $list = $rams->getNextList($list);
  }

  echo "\n\nGet storage list for the current year:";
  $list = $rams->getStorageList("year=curr;month=all");
  foreach($list->feed->entry as $e) {
    echo "\nStorage for year = " . $e->content->params->year . " and month = " . $e->content->params->month; 
    echo " - volume = " . $e->content->params->volume . " - last updated = " . $e->content->params->updated; 
  }
  
  echo "\n\nGet active filter list:";
  $list = $rams->getFilterList("active=1");
  foreach($list->feed->entry as $e) {
    echo "\nFilter with name = " . $e->content->params->name . ", active = " . $e->content->params->active; 
    echo " - filter string = '" . $e->content->params->filter . "', operator = '" . $e->content->params->operator  . "', threshold = " . $e->content->params->threshold . " - last_threshold_warning = " . $e->content->params->last_threshold_warning; 
  }
  
  echo "\n\nGet traffic types:";
  $list = $rams->getTrafficTypeList();
  foreach($list->feed->entry as $e) {
    echo "\nTraffic type = " . $e->content->params->name; 
    echo " - description = " . $e->content->params->description . " - product = " . $e->content->params->product; 
  }
  
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>