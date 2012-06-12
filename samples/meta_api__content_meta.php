<?php
# Sample to demonstrate usage of the META content resource (meta objects).
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
define('RASS_SERVER', 'rass.cdnXX.rambla.be'); # either 'rass.cdn01.rambla.be' or 'rass.cdn02.rambla.be' (depending on the subCDN on which your account is located)
define('LOCAL_PATH', '/path/to/local/file.mp4'); # add path to local (video) file, to be uploaded to the CDN

require_once 'raws_json/meta.php';
require_once 'raws_json/json_client.php';
require_once 'raws_json/meta_service.php';
require_once 'raws_json/rass_service.php';

try {
  $meta = new MetaService(USER, PWD);
  $rass = new RassService(USER, PWD, RASS_SERVER);

  # upload files to CDN (RASS PUT item)
  $item1 = $rass->createItem("/test/content/", "big_buck_bunny.mp4", LOCAL_PATH, True);
  $item1b = $rass->createItem("/test/content/", "big_buck_bunny.jpg", LOCAL_PATH, True);
  $item2 = $rass->createItem("/test/content/", "sintel.mp4", LOCAL_PATH, True);
  
  # create vocabs
  if (! $meta->vocabExists("media")) {
    $meta->createVocab("media", "http://search.yahoo.com/mrss/");
  }
  if (! $meta->vocabExists("myvocab")) {
    $meta->createVocab("myvocab", "http://myvocab.org/ns/");
  }

  # Create content instance with multiple languages for "/test/content/big_buck_bunny.mp4"
  $content_obj1 = new MetaContent("big_buck_bunny");
  $content_obj1->add_file_obj($item1->entry->content->params->path);
  $content_obj1->add_file_obj($item1b->entry->content->params->path);
  $content_obj1->add_meta_obj("title", "media", "Big Buck Bunny", "en");
  $content_obj1->add_meta_obj("description", "media", "Big Buck Bunny is a short computer animated film by the Blender Institute, part of the Blender Foundation.", "en");
  $content_obj1->add_meta_obj("keywords", "media", "bunny, animation, Blender", "en");
  $content_obj1->add_meta_obj("title", "media", "Big Buck Bunny", "nl");
  $content_obj1->add_meta_obj("description", "media", "Big Buck Bunny is een animatiefilm gemaakt door het Blender Institute met behulp van opensource software.", "nl");
  $content_obj1->add_meta_obj("keywords", "media", "konijn, animatie, Blender", "nl");
  $content_obj1->add_meta_obj("tag", "myvocab", "animation", "en");
  $content_obj1->add_meta_obj("tag", "myvocab", "animatie", "nl");
  $content_obj1->add_meta_obj("credit", "media", "Blender Foundation", null, array("role" => "author"));
  $content = $meta->createContent($content_obj1->to_entry());
  echo "\nCreated new content instance with name = " . $content->entry->content->params->name . "\n";

  # get content instance
  $content = $meta->getContentInstance($content->entry->content->params->name);
  $content_obj = new MetaContent();
  $content_obj->from_entry($content);
  echo "\nRetrieved content instance with id = " . $content->entry->id . " and meta objs: ";
  foreach($content_obj->get_meta_objs() as $m) {
    echo "\n- meta obj with vocab = $m->vocab, meta_name = $m->meta_name, text = $m->text, lang = $m->lang";
    if ($m->has_attrs()) {
      echo "\n   meta obj attrs:";
      foreach ($m->get_attrs() as $key => $value) {
        echo "\n   * $key:$value";
      }
    }
  }

  # Create content instance without meta objects for "/test/content/sintel.mp4"
  $content_obj2 = new MetaContent("sintel");
  $content_obj2->add_file_obj($item2->entry->content->params->path);
  $content = $meta->createContent($content_obj2->to_entry());
  echo "\nCreated new content instance with name = " . $content->entry->content->params->name . "\n";

  # Update content instance, adding meta objects
  $content_obj2->from_entry($content);
  $content_obj2->add_meta_obj("title", "media", "Sintel");
  $content_obj2->add_meta_obj("description", "media", "Sintel (code-named Durian) is a short computer animated film by the Blender Institute, part of the Blender Foundation.");
  $content_obj2->add_meta_obj("tag", "myvocab", "animation", "en");
  $content = $meta->updateContent($content_obj2->to_entry());
  echo "\nAdded meta objects to content instance with name = " . $content->entry->content->params->name . "\n";
  
  # search for content
  echo "\n\nGetting content list, search on 'iqmeta=blender' ...";
  $feed = $meta->getContentList('iqmeta=blender');
  foreach($feed->feed->entry as $content_entry) {
    $content_obj = new MetaContent();
    $content_obj->from_entry($content_entry);
    echo "\n\nRetrieved content instance with id = " . $content_obj->id . " and meta objs: ";
    foreach($content_obj->get_meta_objs() as $m) {
      echo "\n- meta obj with vocab = $m->vocab, meta_name = $m->meta_name, text = $m->text, lang = $m->lang";
      if ($m->has_attrs()) {
        echo "\n   meta obj attrs:";
        foreach ($m->get_attrs() as $key => $value) {
          echo "\n   * $key:$value";
        }
      }
    }
  }
  echo "\n... finished retrieving content instances.\n";
  
  # delete content instances + files from CDN
  $meta->deleteContent($content_obj1->name);
  echo "\nDeleted content with name: " . $content_obj1->name . "\n";
  $meta->deleteContent($content_obj2->name);
  echo "\nDeleted content with name: " . $content_obj2->name . "\n";
    
}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>