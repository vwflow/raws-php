<?php

class MetaObj
{
  var $meta_name = "";
  var $vocab = "";
  var $text = "";
  var $lang = "";
	
	function __construct($meta_name = null, $vocab = null, $text = null, $lang = null) 
	{
	  $this->clear();
	  if ($vocab) { 
        $this->vocab = $vocab;
    }
    if ($meta_name) {
        $this->meta_name = $meta_name;
    }
    if ($text) {
        $this->text = $text;
    }
    if ($lang) {
        $this->lang = $lang;
    }
	}
	
	function clear()
  {
    $this->meta_name = "";
    $this->vocab = "";
    $this->text = "";
    $this->lang = "";
  }
  
  function from_dict($dict_data)
  {
    if (in_array("vocab", $dict_data)) {
      $this->vocab = $dict_data["vocab"];
    }
    if (in_array("meta_name", $dict_data)) {
      $this->meta_name = $dict_data["meta_name"];
    }
    if (in_array("text", $dict_data)) {
      $this->text = $dict_data["text"];
    }
    if (in_array("lang", $dict_data)) {
      $this->lang = $dict_data["lang"];
    }
  }

  function to_dict() 
  {
    $d = array();
    $d["vocab"] = $this->vocab;
    $d["meta_name"] = $this->meta_name;
    $d["text"] = $this->text;
    $d["lang"] = $this->lang;
    return $d;
  }
}

class FileObj
{
  var $path = "";
  var $media_type = "";
  var $size = "";
  var $duration = "";
  var $container = "";
  var $bitrate = "";
  var $width = "";
  var $height = "";
  var $frames = "";
  var $framerate = "";
  var $samplerate = "";

  function __construct($path = null, $media_type = null, $size = null, $duration = null, 
                      $container = null, $bitrate = null, $width = null, $height = null, 
                      $frames = null, $framerate = null, $samplerate = null)
  {
	  $this->clear();
	  if ($path) { 
      $this->set_path($path);
    }
    if ($media_type) {
      $this->media_type = $media_type;
    }
    if ($duration) {
      $this->duration = $duration;
    }
    if ($size) {
      $this->size = $size;
    }
    if ($container) {
      $this->container = $container;
    }
    if ($bitrate) {
      $this->bitrate = $bitrate;
    }
    if ($width) {
      $this->width = $width;
    }
    if ($height) {
      $this->height = $height;
    }
    if ($frames) {
      $this->frames = $frames;
    }
    if ($framerate) {
      $this->framerate = $framerate;
    }
    if ($samplerate) {
      $this->samplerate = $samplerate;
    }
  }
  
	function clear()
  {
    $this->path = "";
    $this->filename = "";
    $this->extension = "";
    $this->media_type = "";
    $this->duration = "";
    $this->size = "";
    $this->container = "";
    $this->bitrate = "";
    $this->width = "";
    $this->height = "";
    $this->frames = "";
    $this->framerate = "";
    $this->samplerate = "";
  }
  
  function set_path($path) 
  {
    $this->path = "/" . trim($path, "/");
  }

  
  function from_dict($dict_data)
  {
    if (in_array("path", $dict_data)) {
      $this->path = $dict_data["path"];
    }
    if (in_array("filename", $dict_data)) {
      $this->filename = $dict_data["filename"];
    }
    if (in_array("extension", $dict_data)) {
      $this->extension = $dict_data["extension"];
    }
    if (in_array("media_type", $dict_data)) {
      $this->media_type = $dict_data["media_type"];
    }
    if (in_array("duration", $dict_data)) {
      $this->duration = $dict_data["duration"];
    }
    if (in_array("size", $dict_data)) {
      $this->size = $dict_data["size"];
    }
    if (in_array("container", $dict_data)) {
      $this->container = $dict_data["container"];
    }
    if (in_array("bitrate", $dict_data)) {
      $this->bitrate = $dict_data["bitrate"];
    }
    if (in_array("width", $dict_data)) {
      $this->width = $dict_data["width"];
    }
    if (in_array("height", $dict_data)) {
      $this->height = $dict_data["height"];
    }
    if (in_array("frames", $dict_data)) {
      $this->frames = $dict_data["frames"];
    }
    if (in_array("framerate", $dict_data)) {
      $this->framerate = $dict_data["framerate"];
    }
    if (in_array("samplerate", $dict_data)) {
      $this->samplerate = $dict_data["samplerate"];
    }
  }

  function to_dict() 
  {
    $d = array();
    $d["path"] = $this->path;
    $d["media_type"] = $this->media_type;
    $d["duration"] = $this->duration;
    $d["size"] = $this->size;
    $d["container"] = $this->container;
    $d["bitrate"] = $this->bitrate;
    $d["width"] = $this->width;
    $d["height"] = $this->height;
    $d["frames"] = $this->frames;
    $d["framerate"] = $this->framerate;
    $d["samplerate"] = $this->samplerate;
    return $d;
  }
}


class MetaContent
{
  var $id;
  var $name;
  var $yt_id;
  var $file_objs;
  var $tags;
  var $meta_objs;
  var $thumb_used;
  var $update_files;
  
  function __construct($name = null, $file_objs = null, $tags = null, $meta_objs = null, $thumb_used = null, $update_files = null, $yt_id = null)
  {
	  $this->clear();
    if ($name) {
      $this->name = $name;
    }
    if ($file_objs) {
      $this->file_objs = $file_objs;
    }
    if ($tags) {
      $this->tags = $tags;
    }
    if ($meta_objs) {
      $this->meta_objs = $meta_objs;
    }
	  if ($thumb_used) { 
      $this->set_thumb_used($thumb_used);
    }
    if ($update_files) {
      $this->update_files = $update_files;
    }
    if ($yt_id) {
      $this->yt_id = $yt_id;
    }
  }

	function clear()
  {
    # files
    $this->file_objs = array(); # list of file objects
    # params
    $this->name = null;
    $this->meta_updated = null;
    $this->yt_id = null;
    $this->tags = array(); # list of tag strings
    $this->meta_objs = array(); # list of meta objects
    # file_params
    $this->thumb_used = "";
    $this->update_files = 0;
  }
  
  function set_thumb_used($thumb_used) 
  {
    $this->thumb_used = "/" . trim($thumb_used, "/");
  }
  
  function from_entry($entry)
  {
    $this->clear();
    $this->id = $entry->entry->id;
    $this->name = $entry->entry->content->params->name;
    $this->meta_updated = $entry->entry->content->params->meta_updated;
    $this->yt_id = $entry->entry->content->params->yt_id;
    $this->thumb_used = $entry->entry->content->file_params->thumb_used;
    $this->update_files = $entry->entry->content->file_params->update_files;
    # set $tags
    if (property_exists($entry->entry->content->params, "tag")) {
      foreach ($entry->entry->content->params->tag as $t) {
        array_push($this->tags, $t);
      }
    }
    # set meta objs
    if (property_exists($entry->entry->content->params, "meta")) {
      foreach ($entry->entry->content->params->meta as $m) {
        array_push($this->meta_objs, new MetaObj($m->meta_name, $m->vocab, $m->text, $m->lang));
      }
    }
    # set files
    foreach ($entry->entry->content->file as $f) {
      array_push($this->file_objs, new FileObj($f->path, $f->media_type, $f->size, $f->duration, $f->container, $f->bitrate, $f->width, $f->height, $f->frames, $f->framerate, $f->samplerate));
    } 
  }

  function to_entry() 
  {
    $entry = array("entry" => array("content" => array()));
    # set file array
    $entry["entry"]["content"]["file"] = array();
    foreach ($this->file_objs as $f) {
      array_push($entry["entry"]["content"]["file"], $f->to_dict());
    }
    # set params dict
    $entry["entry"]["content"]["params"] = array();
    $entry["entry"]["content"]["params"]["name"] = $this->name;
    $entry["entry"]["content"]["params"]["yt_id"] = $this->yt_id;
    $entry["entry"]["content"]["params"]["tag"] = $this->tags;
    if (! empty($this->meta_objs) ) {
      $entry["entry"]["content"]["params"]["meta"] = array();
      foreach ($this->meta_objs as $m) {
        array_push($entry["entry"]["content"]["params"]["meta"], $m->to_dict());
      }
    }
    # set file_params dict
    $entry["entry"]["content"]["file_params"] = array();
    $entry["entry"]["content"]["file_params"]["thumb_used"] = $this->thumb_used;
    $entry["entry"]["content"]["file_params"]["update_files"] = $this->update_files;

    return $entry;
  }
}


?>