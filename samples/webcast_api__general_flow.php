<?php
# Sample to demonstrate the basic flow of creating and updating a webcast.
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
define('LOCAL_PATH_TO_JPG', '../../test_resources/rambla.png'); # add path to local (video) file, to be uploaded to the CDN
define('LOCAL_PATH_TO_VIDEO', '../../test_resources/rambla.mp4'); # add path to local (video) file, to be uploaded to the CDN

require_once 'raws_json/meta.php';
require_once 'raws_json/json_client.php';
require_once 'raws_json/webcast_service.php';
require_once 'raws_json/meta_service.php';
require_once 'raws_json/rass_service.php';

try {
  $metawc = new WebcastService(USER, PWD, "meta.meta03.rambla.be");
  $rass = new RassService(USER, PWD, "rass.cdn03.rambla.be");
  $meta = new MetaService(USER, PWD, "meta.meta03.rambla.be");

  # create the webcast, letting the META service create the SMIL file and publish it on the CDN
  $content_name = null;
  $webcast = $metawc->createWebcast(1, "webcast_1", "Hello Webcast 1", "http://myywebcast1.org/", "bruno", "mystream");
  echo "\nCreated webcast: " . $webcast->entry->id . " attached to content instance with name: ";
  foreach($webcast->entry->content->content as $c) {
    $content_name = $c->name;
    echo $content_name;
  } 

  # upload slides and let the META services kwnow about them (attach them to the webcast)
  $item = $rass->createItem("/webcast1/", "slide1.jpg", LOCAL_PATH_TO_JPG);
  $time1 = time();
  $wslide = $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, $time1, 2000);
  echo "\nCreated wslide instance with timestamp = " . $wslide->entry->content->params->timestamp . ", offset = " . $wslide->entry->content->params->offset . " and URL = " . $wslide->entry->content->params->url;
  sleep(2);
  $item = $rass->createItem("/webcast1/", "slide2.jpg", LOCAL_PATH_TO_JPG, True);
  $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, time(), 2 * 2000);
  sleep(2);
  $item = $rass->createItem("/webcast1/", "slide3.jpg", LOCAL_PATH_TO_JPG, True);
  $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, time(), 3 * 2000);
  sleep(2);
  $item = $rass->createItem("/webcast1/", "slide4.jpg", LOCAL_PATH_TO_JPG, True);
  $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, time(), 4 * 2000);
  sleep(2);
  $item = $rass->createItem("/webcast1/", "slide5.jpg", LOCAL_PATH_TO_JPG, True);
  $time5 = time();
  $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, $time5, 5 * 2000);
  sleep(2);
  $item = $rass->createItem("/webcast1/", "slide6.jpg", LOCAL_PATH_TO_JPG, True);
  $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, time(), 6 * 2000);
  sleep(2);
  $item = $rass->createItem("/webcast1/", "slide7.jpg", LOCAL_PATH_TO_JPG, True);
  $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, time(), 7 * 2000);
  sleep(2);
  $item = $rass->createItem("/webcast1/", "slide8.jpg", LOCAL_PATH_TO_JPG, True);
  $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, time(), 8 * 2000);
  sleep(2);
  $item = $rass->createItem("/webcast1/", "slide9.jpg", LOCAL_PATH_TO_JPG, True);
  $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, time(), 9 * 2000);
  sleep(2);
  $item = $rass->createItem("/webcast1/", "slide10.jpg", LOCAL_PATH_TO_JPG, True);
  $metawc->createWslide($webcast->entry->content->params->id, $item->entry->content->params->path, time(), 10 * 2000);

  # get slides starting from slide 1
  echo "\n\n-> getting slides starting from slide 1:";
  $wslides = $metawc->getWslideList($webcast->entry->content->params->id, "timestamp=" . $time1);
  foreach ($wslides->feed->entry as $slide_entry) {
    echo "\nRetrieved wslide entry with timestamp = " . $slide_entry->content->params->timestamp . " and URL = " . $slide_entry->content->params->url;
  }
  # get slides starting from slide 5
  echo "\n\n-> getting slides starting from slide 5:";
  $wslides = $metawc->getWslideList($webcast->entry->content->params->id, "timestamp=" . $time5);
  foreach ($wslides->feed->entry as $slide_entry) {
    echo "\nRetrieved wslide entry with timestamp = " . $slide_entry->content->params->timestamp . " and URL = " . $slide_entry->content->params->url;
  }
  
  # get all slides
  echo "\n\n-> getting ALL slides:";
  $wslides = $metawc->getWslideList($webcast->entry->content->params->id);
  foreach ($wslides->feed->entry as $slide_entry) {
    echo "\nRetrieved wslide entry with timestamp = " . $slide_entry->content->params->timestamp . " and URL = " . $slide_entry->content->params->url;
  }
  
  # upload on-demand video to cdn03
  $item = $rass->createItem("/webcast1/", "webcast.mp4", LOCAL_PATH_TO_VIDEO);
  # attach it to the content object (and thereby also to the webcast)
  $content = $meta->getContentInstance($content_name);
  $content_obj = new MetaContent();
  $content_obj->from_entry($content);
  $content_obj->add_file_obj($item->entry->content->params->path, "video", filesize(LOCAL_PATH_TO_VIDEO), 120);
  $content_obj->add_comment("1000", "First comment", "Description of first comment", "monty");
  $content_obj->add_comment("2000", "Second comment", "Description of second comment", "monty");
  $content_obj->add_chapter("5000", "First chapter", "Description of first chapter");
  $content = $meta->updateContent($content_obj->to_entry());
  $content_obj->from_entry($content);
  echo "\nUpdated content instance with name = " . $content->entry->content->params->name . "\n";
  echo "\nFiles: ";
  foreach ($content_obj->get_file_objs() as $f) {
    echo "\n- file with path = " . $f->path;
  }
  echo "\nFiles: ";
  foreach ($content_obj->get_chapters() as $c) {
    echo "\n- chapter with offset = " . $c->offset . " and title = " . $c->title;
  }
  echo "\nFiles: ";
  foreach ($content_obj->get_comments() as $c) {
    echo "\n- comment with offset = " . $c->offset . " and title = " . $c->title;
  }
  
  # add webcast to wchannel
  $wchannel = $metawc->createWchannel("Channel 1", "Description of Channel 1", $webcast->entry->content->params->id);
  echo "\nCreated wchannel: " . $wchannel->entry->id . " for webcasts:";
  foreach ($wchannel->entry->content->webcast as $wc) {
    echo "\n- " . $wc->href;
  }
  
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>