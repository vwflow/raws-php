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

  echo "\n\nGet traffic list for the root directory:";
  $traffic = $rams->getTrafficList("/", "type=http,rtmp;kind=root");
  foreach($traffic->feed->entry as $e) {
    echo "\nTraffic for type = " . $e->content->params->type . ", kind = " . $e->content->params->kind  . " and path = " . $e->content->params->path; 
    echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - last_request = " . $e->content->params->last_request; 
  }

  echo "\n\nGet traffic list for the sub-directories of the root directory:";
  $traffic = $rams->getTrafficList("/", "type=http,rtmp;kind=dir");
  foreach($traffic->feed->entry as $e) {
    echo "\nTraffic for type = " . $e->content->params->type . ", kind = " . $e->content->params->kind  . " and path = " . $e->content->params->path; 
    echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - last_request = " . $e->content->params->last_request; 
  }

  echo "\n\nGet traffic list for the files inside the root directory, from 2012-01-01 until 2012-09-30 (200 entries at a time):";
  $traffic = $rams->getTrafficList("/", "type=http,rtmp;kind=file;from=2012-01-01;until=2012-09-30;paginate_by=200");
  while ($traffic)
  {
    foreach($traffic->feed->entry as $e) {
      echo "\nTraffic for type = " . $e->content->params->type . ", kind = " . $e->content->params->kind  . " and path = " . $e->content->params->path; 
      echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - last_request = " . $e->content->params->last_request; 
    }
    $traffic = $rams->getNextList($traffic);
  }
  
  echo "\n\nGet traffic list for the files inside the root directory, for the current month:";
  $traffic = $rams->getTrafficList("/", "type=http,rtmp;kind=file;year=curr;month=curr");
  foreach($traffic->feed->entry as $e) {
      echo "\nTraffic for type = " . $e->content->params->type . ", kind = " . $e->content->params->kind  . " and path = " . $e->content->params->path; 
      echo " - volume = " . $e->content->params->volume . " - hits = " . $e->content->params->hits  . " - last_request = " . $e->content->params->last_request; 
      # NOTE: the following properties require statistics layers to be enabled for your user account (see https://racp.rcms.rambla.be/?q=content/setting-statistics-filters)
      echo "Last Request: " . $e->content->params->last_request . "\n";
      echo "Seconds Average: " . $e->content->params->sec_avg . "\n";
      echo "Seconds Total: " . $e->content->params->sec_total . "\n";
      echo "Pct 0_10: " . $e->content->params->pct_0_10 . "\n";
      echo "Pct 10_20: " . $e->content->params->pct_10_20 . "\n";
      echo "Pct 20_30: " . $e->content->params->pct_20_30 . "\n";
      echo "Pct 30_40: " . $e->content->params->pct_30_40 . "\n";
      echo "Pct 40_50: " . $e->content->params->pct_40_50 . "\n";
      echo "Pct 50_60: " . $e->content->params->pct_50_60 . "\n";
      echo "Pct 60_70: " . $e->content->params->pct_60_70 . "\n";
      echo "Pct 70_80: " . $e->content->params->pct_70_80 . "\n";
      echo "Pct 80_90: " . $e->content->params->pct_80_90 . "\n";
      echo "Pct 90_100: " . $e->content->params->pct_90_100 . "\n";
      echo "Pct Unknown: " . $e->content->params->pct_unknown . "\n";
      echo "time0: " . $e->content->params->time0 . "\n";
      echo "time1: " . $e->content->params->time1 . "\n";
      echo "time2: " . $e->content->params->time2 . "\n";
      echo "time3: " . $e->content->params->time3 . "\n";
      echo "time4: " . $e->content->params->time4 . "\n";
      echo "time5: " . $e->content->params->time5 . "\n";
      echo "time6: " . $e->content->params->time6 . "\n";
      echo "time7: " . $e->content->params->time7 . "\n";
      echo "time8: " . $e->content->params->time8 . "\n";
    }
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>