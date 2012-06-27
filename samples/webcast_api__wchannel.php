<?php
# Sample to demonstrate usage of the META wchannel resource.
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

  # create wchannel
  $wchannel = $meta->createWchannel("myywchannel", "Description of myywchannel", "1");
  echo "\nCreated wchannel: " . $wchannel->entry->id . "\n";

  # get wchannel instance
  $wchannel = $meta->getWchannelInstance($wchannel->entry->content->params->id);
  echo "\nRetrieved wchannel with title: " . $wchannel->entry->content->params->title . "\n";
  
  # update wchannel instance
  $wchannel->entry->content->params->description = "My updated wchannel"; # update description
  $wchannel->entry->content->action = new stdClass;
  $wchannel->entry->content->action->update_webcast = True;
  $webcast = $meta->createWebcast(1004, "mywebcast", "Hello Webcast", "See http://myywebcast.org/", "monty", "mystream");
  $wchannel->entry->content->webcast = array();
  $wc = new stdClass;
  $wc->id = $webcast->entry->content->params->id;
  $wc->ranking = 5;
  $wchannel->entry->content->webcast[] = $wc; # add webcast to this channel
  $wchannel = $meta->updateWchannel($wchannel);
  echo "\nUpdated wchannel, new description = " . $wchannel->entry->content->params->description . "\n";
    
  # get all wchannel instances
  echo "\nGetting wchannel list...";
  $feed = $meta->getWchannelList();
  foreach($feed->feed->entry as $e) {
    echo "\n* retrieved wchannel: " . $e->id . " with description = " . $e->content->params->description;
  }
  echo "\n... finished retrieving wchannel instances.\n";
  
  # delete wchannel instances
  $meta->deleteWchannel($wchannel->entry->content->params->id);
  echo "\nDeleted wchannel with id: " . $wchannel->entry->content->params->id . "\n";
  
  # delete webcast instance
  $meta->deleteWebcast($webcast->entry->content->params->id, False);
  echo "\nDeleted webcast with id: " . $webcast->entry->content->params->id . "\n";
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>