<?php
# Sample to demonstrate usage of the META webcast resource.
# DISCLAIMER: This functionality has not yet been released, this script is for development purposes only !
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
require_once 'raws_json/webcast_service.php';

try {
  $meta = new WebcastService(USER, PWD, "meta.meta03.rambla.be");

  $wchannel = $meta->createWchannel("channel09", "Description of channel09");

  # create webcast with custom smil and wchannel linked
  $smil_streams = array();
  $smil_streams[] = array("name" => "mystream_lq", "bitrate" => 200000);
  $smil_streams[] = array("name" => "mystream_hq", "bitrate" => 800000);
  $wchannels = array();
  $wchannels[] = array("id" => $wchannel->entry->content->params->id, "ranking" => 9);
  $webcast = $meta->createWebcast("99", "webcast99", "Hello Webcast 99", "See http://myywebcast99.org/", "monty", "mystream99", $smil_streams, $wchannels);
  echo "\nCreated webcast: " . $webcast->entry->id . "\n";
    
  # get webcast instance
  $webcast = $meta->getWebcastInstance($webcast->entry->content->params->id);
  echo "\nRetrieved webcast with title: " . $webcast->entry->content->params->title . "\n";
  
  # other way to call updateWebcast, passing the webcast id as second argument
  $webcast->entry->content->params->description = "My updated webcast"; # update webcast description
  $webcast->entry->content->action = new stdClass;
  $webcast->entry->content->action->update_wchannel = True;
  foreach($webcast->entry->content->wchannel as $channel) {
    $channel->ranking = 1;  # update channel ranking
  }
  $webcast = $meta->updateWebcast($webcast, $webcast->entry->content->params->id); 
  
  # get all webcast instances
  echo "\nGetting webcast list...";
  $feed = $meta->getWebcastList();
  foreach($feed->feed->entry as $e) {
    echo "\n* retrieved webcast: " . $e->id . " with description = " . $e->content->params->description;
  }
  echo "\n... finished retrieving webcast instances.\n";
  
  # delete webcast instance
  $meta->deleteWebcast($webcast->entry->content->params->id, False);
  echo "\nDeleted webcast with id: " . $webcast->entry->content->params->id . "\n";
  
  # delete wchannel instance
  $meta->deleteWchannel($wchannel->entry->content->params->id);
  echo "\nDeleted wchannel with id: " . $wchannel->entry->content->params->id . "\n";
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>