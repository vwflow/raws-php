<?php
# Sample to demonstrate how to use the RAMS customer resources.
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
define('USER', "CU-xxx"); # name of your Rambla user account
define('PWD', "xxx"); # password of your Rambla user account

require_once 'raws_json/json_client.php';
require_once 'raws_json/rams_service.php';

try {
  $rams = new RamsService(USER, PWD);

  echo "\n\nGet users:";
  $traffic = $rams->getUsersList();
  foreach($traffic->feed->entry as $e) {
    echo "\nUser = " . $e->content->params->name . " with email = " . $e->content->params->email; 
  }

  echo "\n\nGet used list using querystring:";
  $traffic = $rams->getUsedList("from=2012-01;until=2012-12");
  foreach($traffic->feed->entry as $e) {
    echo "\nUsed list for user = " . $e->content->params->name . " and product = " . $e->content->params->product; 
    echo " => number of credits used = " . $e->content->params->credits; 
  }

  echo "\n\nGet payed list using querystring:";
  $traffic = $rams->getPayedList("from=2012-01;until=2012-12");
  foreach($traffic->feed->entry as $e) {
    echo "\nPayed " . $e->content->params->credits . " for formula = " . $e->content->params->formula;
    echo " in year = " . $e->content->params->year . " and month = "  . $e->content->params->month; 
  }
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>