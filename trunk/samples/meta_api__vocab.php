<?php
# Sample to demonstrate usage of the META vocab resource.
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
require_once 'raws_json/meta_service.php';

try {
  $meta = new MetaService(USER, PWD);

  # create vocab
  $vocab = $meta->createVocab("myyvocab", "http://myyvocab.org/");
  echo "\nCreated vocab: " . $vocab->entry->id . "\n";

  # get vocab instance
  $vocab = $meta->getVocabInstance($vocab->entry->content->params->name);
  echo "\nRetrieved vocab with namespace: " . $vocab->entry->content->params->xml_namespace . "\n";

  # update vocab instance
  $vocab->entry->content->params->xml_namespace = "http://mynewvocab.com";
  $vocab = $meta->updateVocab($vocab);
  echo "\nUpdated vocab, new namespace = " . $vocab->entry->content->params->xml_namespace . "\n";

  # other way to call updateVocab, passing the vocab name as second argument
  $vocab->entry->content->params->xml_namespace = "http://mynewvocab2.com";
  $vocab = $meta->updateVocab($vocab, $vocab->entry->content->params->name); 

  # get all vocab instances
  echo "\nGetting vocab list...";
  $feed = $meta->getVocabList();
  foreach($feed->feed->entry as $e) {
    echo "\n* retrieved vocab: " . $e->id . " with namespace = " . $e->content->params->xml_namespace;
  }
  echo "\n... finished retrieving vocab instances.\n";

  # delete vocab instance
  $meta->deleteVocab($vocab->entry->content->params->name);
  echo "\nDeleted vocab with name: " . $vocab->entry->content->params->name . "\n";
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>