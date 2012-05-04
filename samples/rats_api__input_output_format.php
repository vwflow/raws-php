<?php
# Sample to demonstrate how to use the RATS input, output and format resources.
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
require_once 'raws_json/rats_service.php';

try {
  $rats = new RatsService(USER, PWD);

  # INPUT
  # -----
  
  # create input profile
  $input = $rats->createInput(array("name" => "test_rats_api_input", "method" => "cdn"));
  echo "\n\nCreated input: " . $input->entry->id;
  
  # retrieve a single input instance based on the id
  $input = $rats->getInputInstance($input->entry->content->params->id);
  echo "\nRetrieved input with name = " . $input->entry->content->params->name . " and method = " . $input->entry->content->params->method;

  # update input instance
  $input->entry->content->params->description = "Importing files from CDN";
  $input = $rats->updateInput($input);
  echo "\nRetrieved input with name = " . $input->entry->content->params->name . " and description = " . $input->entry->content->params->description;

  # get list of your own input instances
  echo "\nGetting list of my input profiles:";
  $input_list = $rats->getInputList("owner=self");
  foreach($input_list->feed->entry as $e) {
    echo "\n Input has name = " . $e->content->params->name;
  }
  
  # delete input instance
  $rats->deleteInput($input->entry->content->params->id);
  echo "\nDeleted input with name = " . $input->entry->content->params->name;

  # OUTPUT
  # -----
  
  # create output profile
  $output = $rats->createOutput(array("name" => "test_rats_api_output", "method" => "cdn"));
  echo "\n\nCreated output: " . $output->entry->id;
  
  # retrieve a single output instance based on the id
  $output = $rats->getOutputInstance($output->entry->content->params->id);
  echo "\nRetrieved output with name = " . $output->entry->content->params->name . " and method = " . $output->entry->content->params->method;

  # update output instance
  $output->entry->content->params->description = "Exporting files to CDN";
  $output = $rats->updateOutput($output);
  echo "\nRetrieved output with name = " . $output->entry->content->params->name . " and description = " . $output->entry->content->params->description;

  # get list of your own output instances
  echo "\nGetting list of my output profiles:";
  $output_list = $rats->getOutputList("owner=self");
  foreach($output_list->feed->entry as $e) {
    echo "\n Output has name = " . $e->content->params->name;
  }
  
  # delete output instance
  $rats->deleteOutput($output->entry->content->params->id);
  echo "\nDeleted output with name = " . $output->entry->content->params->name;


  # FORMAT
  # -----
  
  # create format profile
  $format = $rats->createFormat(array("name" => "test_rats_api_format", "category" => "formats", "container" => "mp4", "video_codec" => "h264", "video_cq" => "0.12", 
                                      "video_deinterlace" => "md", "video_passes" => "2", "audio_channel" => "2", "audio_codec" => "aac", 
                                      "audio_bitrate" => "96", "audio_sample_rate" => "44100"));
  echo "\n\nCreated format: " . $format->entry->id;
  
  # retrieve a single format instance based on the id
  $format = $rats->getFormatInstance($format->entry->content->params->id);
  echo "\nRetrieved format with name = " . $format->entry->content->params->name . " and container = " . $format->entry->content->params->container;

  # update format instance
  $format->entry->content->params->description = "Basic mp4, using size of the original";
  $format = $rats->updateFormat($format);
  echo "\nRetrieved format with name = " . $format->entry->content->params->name . " and description = " . $format->entry->content->params->description;

  # get list of your own format instances
  echo "\nGetting list of my format profiles:";
  $format_list = $rats->getFormatList("owner=self");
  foreach($format_list->feed->entry as $e) {
    echo "\n Format has name = " . $e->content->params->name;
  }
  
  # delete format instance
  $rats->deleteFormat($format->entry->content->params->id);
  echo "\nDeleted format with name = " . $format->entry->content->params->name;



}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>