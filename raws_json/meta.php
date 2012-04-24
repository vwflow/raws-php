<?php

class MetaObj
{
  var $meta_name = "";
  var $vocab = "";
  var $text = "";
  var $lang = "";
  var $attrs = "";
	
	function __construct($meta_name = null, $vocab = null, $text = null, $lang = null, $attrs = null) 
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
    if ($attrs) {
      $this->set_attrs($attrs);
    }
	}
	
	function clear()
  {
    $this->meta_name = "";
    $this->vocab = "";
    $this->text = "";
    $this->lang = "";
    $this->attrs = array();
  }

  function set_attrs($attrs)
  {
    foreach($attrs as $key => $value) {
      if ($key == "meta_name") { continue; }
      if ($key == "vocab") { continue; }
      if ($key == "text") { continue; }
      if ($key == "lang") { continue; }
      $this->attrs[$key] = $value; 
    }
  }
  
  function has_attrs() {
    $has_attrs = True;
    if (empty($this->attrs)) {
      $has_attrs = False;
    }
    return $has_attrs;
  }

  function get_attrs() {
    return $this->attrs;
  }
  
  function to_array() 
  {
    $d = array();
    $d["vocab"] = $this->vocab;
    $d["meta_name"] = $this->meta_name;
    $d["text"] = $this->text;
    $d["lang"] = $this->lang;
    foreach($this->attrs as $key => $value) {
      $d[$key] = $value;
    }
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

  function to_array() 
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
  
  /**
   * Get an array of FileObj objects.
   *
   * @return array of FileObj objects
   */
  function get_file_objs() {
    return $this->file_objs;
  }

  /**
   * Add a single FileObj.
   *
   * @param FileObj $file_obj
   */
  function add_file_obj($path = null, $media_type = null, $size = null, $duration = null, 
                      $container = null, $bitrate = null, $width = null, $height = null, 
                      $frames = null, $framerate = null, $samplerate = null)
  {
    array_push($this->file_objs, new FileObj($path, $media_type, $size, $duration, $container, $bitrate, $width, $height, $frames, $framerate, $samplerate));
  }

  /**
   * Add an array of FileObj objects.
   *
   * @param array $file_objs Array of FileObj objects
   */
  function add_file_objs($file_objs) {
    array_merge($this->file_objs, $file_objs);
  }
    
  /**
   * Get an array of MetaObj objects.
   *
   * @return array of MetaObj objects
   */
  function get_meta_objs() {
    return $this->meta_objs;
  }

  /**
   * Add a single MetaObj.
   *
   * @param MetaObj $meta_obj
   */
  function add_meta_obj($meta_name = null, $vocab = null, $text = null, $lang = null, $attrs = null) {
    array_push($this->meta_objs, new MetaObj($meta_name, $vocab, $text, $lang, $attrs));
  }

  /**
   * Add an array of MetaObj objects.
   *
   * @param array $meta_objs Array of MetaObj objects
   */
  function add_meta_objs($meta_objs) {
    array_merge($this->meta_objs, $meta_objs);
  }

  /**
   * Set all meta_objs (replaces existing ones).
   *
   * @param array $meta_objs Array of MetaObj objects
   */
  function set_meta_objs($meta_objs) {
    $this->meta_objs = $meta_objs;
  }
  
  
  /**
   * Get tags.
   *
   * @return array of tag strings
   */
  function get_tags() {
    return $this->tags;
  }

  /**
   * Add a single tag.
   *
   * @param string $tag
   */
  function add_tag($tag) {
    array_push($this->tags, $tag);
  }

  /**
   * Add an array of tags.
   *
   * @param array $tags Array of strings
   */
  function add_tags($tags) {
    array_merge($this->tags, $tags);
  }
  
  /**
   * Set all tags (replaces existing ones).
   *
   * @param array $tags Array of strings
   */
  function set_tags($tags) {
    $this->tags = $tags;
  }
  
  function set_thumb_used($thumb_used) 
  {
    $this->thumb_used = "/" . trim($thumb_used, "/");
  }
  
  function from_entry($entry)
  {
    $this->clear();
    if (property_exists($entry, "entry")) {
      $this->id = $entry->entry->id;
      $this->name = $entry->entry->content->params->name;
      $this->meta_updated = $entry->entry->content->params->meta_updated;
      $this->yt_id = $entry->entry->content->params->yt_id;
      $this->thumb_used = $entry->entry->content->file_params->thumb_used;
      $this->update_files = $entry->entry->content->file_params->update_files;
      # set tags
      if (property_exists($entry->entry->content->params, "tag")) {
        foreach ($entry->entry->content->params->tag as $t) {
          array_push($this->tags, $t);
        }
      }
      # set meta objs
      if (property_exists($entry->entry->content->params, "meta")) {
        foreach ($entry->entry->content->params->meta as $m) {
          array_push($this->meta_objs, new MetaObj($m->meta_name, $m->vocab, $m->text, $m->lang, get_object_vars($m)));
        }
      }
      # set files
      foreach ($entry->entry->content->file as $f) {
        array_push($this->file_objs, new FileObj($f->path, $f->media_type, $f->size, $f->duration, $f->container, $f->bitrate, $f->width, $f->height, $f->frames, $f->framerate, $f->samplerate));
      }
    }
    elseif (property_exists($entry, "id")) {
      $this->id = $entry->id;
      $this->name = $entry->content->params->name;
      $this->meta_updated = $entry->content->params->meta_updated;
      $this->yt_id = $entry->content->params->yt_id;
      $this->thumb_used = $entry->content->file_params->thumb_used;
      $this->update_files = $entry->content->file_params->update_files;
      # set tags
      if (property_exists($entry->content->params, "tag")) {
        foreach ($entry->content->params->tag as $t) {
          array_push($this->tags, $t);
        }
      }
      # set meta objs
      if (property_exists($entry->content->params, "meta")) {
        foreach ($entry->content->params->meta as $m) {
          array_push($this->meta_objs, new MetaObj($m->meta_name, $m->vocab, $m->text, $m->lang, get_object_vars($m)));
        }
      }
      # set files
      foreach ($entry->content->file as $f) {
        array_push($this->file_objs, new FileObj($f->path, $f->media_type, $f->size, $f->duration, $f->container, $f->bitrate, $f->width, $f->height, $f->frames, $f->framerate, $f->samplerate));
      }
    }
  }

  function to_entry() 
  {
    $entry = array("entry" => array("content" => array()));
    # set file array
    $entry["entry"]["content"]["file"] = array();
    foreach ($this->file_objs as $f) {
      array_push($entry["entry"]["content"]["file"], $f->to_array());
    }
    # set params dict
    $entry["entry"]["content"]["params"] = array();
    $entry["entry"]["content"]["params"]["name"] = $this->name;
    $entry["entry"]["content"]["params"]["yt_id"] = $this->yt_id;
    $entry["entry"]["content"]["params"]["tag"] = $this->tags;
    if (! empty($this->meta_objs) ) {
      $entry["entry"]["content"]["params"]["meta"] = array();
      foreach ($this->meta_objs as $m) {
        array_push($entry["entry"]["content"]["params"]["meta"], $m->to_array());
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