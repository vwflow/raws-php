<?php
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

/**
 * @file Helper objects corresponding to resources of the META service.
 *
 * @see https://wiki.rambla.be/META_content_resource
 * @package	raws-php
 * @copyright rambla.eu, 2012
 * @version 0.1 (2012/04/26)
 */
 
/**
 * Class corresponding to a meta object, which may be part of the 'params' object inside a 'content' entry.
 *
 * @see https://wiki.rambla.be/META_content_resource#The_.27params.27_object
 */
class MetaObj
{
  var $meta_name = "";
  var $vocab = "";
  var $text = "";
  var $lang = "";
  var $attrs = "";
	
  /**
   * Constructor.
   *
   * @see https://wiki.rambla.be/RAWS_meta_object
   * @param string $meta_name The name of the metadata property (case-sensitive)
   * @param string $vocab The name of a vocab instance
   * @param string $text The metadata value
   * @param string $lang ISO 639-1 abbreviation of the language being used
   * @param array $attrs Associative array containing additional attributes (key, value are both strings) for this meta object.
   */
	function __construct($meta_name = null, $vocab = null, $text = null, $lang = null, $attrs = null) 
	{
	  $this->clear();
	  if ($vocab !== '') { 
        $this->vocab = $vocab;
    }
    if ($meta_name !== '') {
        $this->meta_name = $meta_name;
    }
    if ($text !== '') {
        $this->text = $text;
    }
    if ($lang !== '') {
        $this->lang = $lang;
    }
    if ($attrs) {
      $this->set_attrs($attrs);
    }
	}
	
  /**
   * Clears this object's data.
   */
	function clear()
  {
    $this->meta_name = "";
    $this->vocab = "";
    $this->text = "";
    $this->lang = "";
    $this->attrs = array();
  }

  /**
   * Set the extra attrs array.
   *
   * @param array $attrs Associative array containing additional attributes (key, value are both strings) for the meta objects.
   */
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
  
  /**
   * Does this meta object have additional attrs.
   *
   * @return bool True if at least one additional attr.
   */
  function has_attrs() {
    $has_attrs = True;
    if (empty($this->attrs)) {
      $has_attrs = False;
    }
    return $has_attrs;
  }

  /**
   * Get additional attrs.
   *
   * @return array Associative array containing additional attributes (key, value are both strings) for this meta object
   */
  function get_attrs() {
    return $this->attrs;
  }
  
  /**
   * Store the object's datamembers in an array (that can serve as input for encoding the object into json)
   *
   * @return array Associative array (key, value are both strings).
   */
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

/**
 * Class corresponding to a file object, which may be part of the 'file' object inside a 'content' entry.
 *
 * @see https://wiki.rambla.be/META_content_resource#The_.27file.27_object
 */
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

  var $filename = "";
  var $extension = "";
  var $mimetype = "";
  
  var $download = "";
  var $rtmp_streamer = "";
  var $rtmp_location = "";
  var $rtsp = "";
  var $apple_http = "";
  var $silverlight = "";
  var $adobe_http = "";
  var $rtmp_adaptive = "";

  var $language = "";
  
  /**
   * Constructor.
   *
   * If you're creating a new File object, it must contain at least one file object with the 'path' property being set.
   * All other properties are optional.
   *
   * @param string $path relative path to the file on the CDN, starting from the root-directory of your user account
   * @see https://wiki.rambla.be/META_content_resource#The_.27file.27_object
   */
  function __construct($path = null, $media_type = null, $size = null, $duration = null, 
                      $container = null, $bitrate = null, $width = null, $height = null, 
                      $frames = null, $framerate = null, $samplerate = null, 
                      $filename = null, $extension = null, $mimetype = null, 
                      $download = null, $rtmp_streamer = null, $rtmp_location = null, $rtsp = null, 
                      $apple_http = null, $silverlight = null, $adobe_http = null, $rtmp_adaptive = null, $language = null)
  {
	  $this->clear();
	  if ($path) { 
      $this->set_path($path);
    }
    if ($media_type !== '') {
      $this->media_type = $media_type;
    }
    if (! is_null($duration)) {
      $this->duration = $duration;
    }
    if (! is_null($size)) {
      $this->size = $size;
    }
    if ($container !== '') {
      $this->container = $container;
    }
    if (! is_null($bitrate)) {
      $this->bitrate = $bitrate;
    }
    if (! is_null($width)) {
      $this->width = $width;
    }
    if (! is_null($height)) {
      $this->height = $height;
    }
    if (! is_null($frames)) {
      $this->frames = $frames;
    }
    if (! is_null($framerate)) {
      $this->framerate = $framerate;
    }
    if (! is_null($samplerate)) {
      $this->samplerate = $samplerate;
    }
    if (! is_null($filename)) {
      $this->filename = $filename;
    }
    if (! is_null($extension)) {
      $this->extension = $extension;
    }
    if (! is_null($mimetype)) {
      $this->mimetype = $mimetype;
    }
    if (! is_null($download)) {
      $this->download = $download;
    }
    if (! is_null($rtmp_streamer)) {
      $this->rtmp_streamer = $rtmp_streamer;
    }
    if (! is_null($rtmp_location)) {
      $this->rtmp_location = $rtmp_location;
    }
    if (! is_null($rtsp)) {
      $this->rtsp = $rtsp;
    }
    if (! is_null($apple_http)) {
      $this->apple_http = $apple_http;
    }
    if (! is_null($silverlight)) {
      $this->silverlight = $silverlight;
    }
    if (! is_null($adobe_http)) {
      $this->adobe_http = $adobe_http;
    }
    if (! is_null($rtmp_adaptive)) {
      $this->rtmp_adaptive = $rtmp_adaptive;
    }
    if (! is_null($language)) {
      $this->language = $language;
    }
  }
  
  /**
   * Clears this object's data.
   */
	function clear()
  {
    $this->path = "";
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

    $this->filename = "";
    $this->extension = "";
    $this->mimetype = "";

    $this->download = "";
    $this->rtmp_streamer = "";
    $this->rtmp_location = "";
    $this->rtsp = "";
    $this->apple_http = "";
    $this->silverlight = "";
    $this->adobe_http = "";
    $this->rtmp_adaptive = "";

    $this->language = "";
  }
  
  /**
   * Set path according to META conventions.
   */
  function set_path($path) 
  {
    $this->path = "/" . trim($path, "/");
  }

  /**
   * Store the object's datamembers in an array (that can serve as input for encoding the object into json)
   *
   * @return array Associative array (key, value are both strings).
   */
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

    $d["filename"] = $this->filename;
    $d["extension"] = $this->extension;
    $d["mimetype"] = $this->mimetype;

    $d["download"] = $this->download;
    $d["rtmp_streamer"] = $this->rtmp_streamer;
    $d["rtmp_location"] = $this->rtmp_location;
    $d["rtsp"] = $this->rtsp;
    $d["apple_http"] = $this->apple_http;
    $d["silverlight"] = $this->silverlight;
    $d["adobe_http"] = $this->adobe_http;
    $d["rtmp_adaptive"] = $this->rtmp_adaptive;

    $d["language"] = $this->language;

    return $d;
  }
}

/**
 * Class corresponding to a chapter, which may be part of a 'content' entry.
 *
 * @see https://wiki.rambla.be/META_content_resource
 */
class Chapter
{
  var $id = "";
  var $offset = "";
  var $title = "";
  var $description = "";
	
  /**
   * Constructor.
   *
   * @see https://wiki.rambla.be/RAWS_meta_object
   * @param string $offset Offset from the beginning of a video
   * @param string $title Chapter title
   * @param string $description Chapter description
   */
	function __construct($offset = null, $title = null, $description = null, $id = null) 
	{
	  $this->clear();
    if (! is_null($id)) {
        $this->id = $id;
    }
    if (! is_null($offset)) {
        $this->offset = $offset;
    }
    if ($title !== '') {
        $this->title = $title;
    }
    if ($description !== '') {
        $this->description = $description;
    }
	}
	
  /**
   * Clears this object's data.
   */
	function clear()
  {
    $this->id = "";
    $this->offset = "";
    $this->title = "";
    $this->description = "";
  }

  /**
   * Store the object's datamembers in an array (that can serve as input for encoding the object into json)
   *
   * @return array Associative array (key, value are both strings).
   */
  function to_array() 
  {
    $d = array();
    $d["id"] = $this->id;
    $d["offset"] = $this->offset;
    $d["title"] = $this->title;
    $d["description"] = $this->description;
    return $d;
  }
}


/**
 * Class corresponding to a comment, which may be part of a 'content' entry.
 *
 * @see https://wiki.rambla.be/META_content_resource
 */
class Comment
{
  var $id = "";
  var $offset = "";
  var $title = "";
  var $description = "";
  var $author = "";
  var $type = "";
  var $updated = "";
  var $published = "";
  var $publish_time = "";
  var $insert_time = "";
	
  /**
   * Constructor.
   *
   * @see https://wiki.rambla.be/RAWS_meta_object
   * @param string $offset Offset from the beginning of a video
   * @param string $title Comment title
   * @param string $description Comment description
   */
	function __construct($offset = null, $title = null, $description = null, $author = null, $type = null, $updated = null, $published = null, $id = null, $publish_time = null, $insert_time = null) 
	{
	  $this->clear();
	  if (! is_null($offset)) { 
        $this->offset = $offset;
    }
    if ($title !== '') {
        $this->title = $title;
    }
    if ($description !== '') {
        $this->description = $description;
    }
    if ($author !== '') {
        $this->author = $author;
    }
    if ($type !== '') {
        $this->type = $type;
    }
    if (! is_null($updated)) {
        $this->updated = $updated;
    }
    if (! is_null($published)) {
        $this->published = $published;
    }
    if (! is_null($id)) {
        $this->id = $id;
    }
    if (! is_null($publish_time)) {
        $this->publish_time = $publish_time;
    }
    if (! is_null($insert_time)) {
        $this->insert_time = $insert_time;
    }
	}
	
  /**
   * Clears this object's data.
   */
	function clear()
  {
    $this->id = "";
    $this->offset = "";
    $this->title = "";
    $this->description = "";
    $this->author = "";
    $this->type = "";
    $this->updated = "";
    $this->published = "";
    $this->publish_time = "";
    $this->insert_time = "";
  }

  /**
   * Store the object's datamembers in an array (that can serve as input for encoding the object into json)
   *
   * @return array Associative array (key, value are both strings).
   */
  function to_array() 
  {
    $d = array();
    $d["id"] = $this->id;
    $d["offset"] = $this->offset;
    $d["title"] = $this->title;
    $d["description"] = $this->description;
    $d["author"] = $this->author;
    $d["type"] = $this->type;
    $d["updated"] = $this->updated;
    $d["published"] = $this->published;
    $d["publish_time"] = $this->publish_time;
    $d["insert_time"] = $this->insert_time;
    return $d;
  }
}

/**
 * Class corresponding to a 'content' entry.
 *
 * @see https://wiki.rambla.be/META_content_resource
 */
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
  var $chapters;
  var $comments;
  
  /**
   * Constructor.
   *
   * If you're creating a new Content object, it must contain at least one file_obj with the 'path' property being set.
   * For more info, see https://wiki.rambla.be/META_content_resource#Create_new_content_instance
   *
   * @param string $name unique name by which the given media content is known on the CDN (US-ASCII characters only)
   * @param array $file_objs Indexed array of FileObj objects
   * @param array $tags Indexed array of tag strings
   * @param array $meta_objs Indexed array of MetaObj objects
   * @param string $thumb_used Relative path for the thumb to be used in playlists
   * @param int $update_files Set to 1 if you want to update the FileObj's that are linked to an existing content resource (requires all FileObjs to be set on this object)
   * @param string $yt_id Unique ID of the corresponding YouTube video (if any, otherwise empty string) 
   * @param array $chapters Indexed array of Chapter objects
   * @param array $comments Indexed array of Comment objects
   * @see https://wiki.rambla.be/META_content_resource#Content_object_details
   */
  function __construct($name = null, $file_objs = null, $tags = null, $meta_objs = null, $thumb_used = null, $update_files = null, $yt_id = null, $chapters = null, $comments = null)
  {
	  $this->clear();
    if ($name !== '') {
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
	  if ($thumb_used !== '') { 
      $this->set_thumb_used($thumb_used);
    }
    if (! is_null($update_files)) {
      $this->update_files = $update_files;
    }
    if (! is_null($yt_id)) {
      $this->yt_id = $yt_id;
    }
    if ($chapters) {
      $this->chapters = $chapters;
    }
    if ($comments) {
      $this->comments = $comments;
    }
  }

  /**
   * Clears this object's data.
   */
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
    # chapters & comments
    $this->chapters = array();
    $this->comments = array();
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
    $this->update_files = 1;
  }

  /**
   * Add an array of FileObj objects.
   *
   * @param array $file_objs Array of FileObj objects
   */
  function add_file_objs($file_objs) {
    array_merge($this->file_objs, $file_objs);
    $this->update_files = 1;
  }
  
  /**
   * Set an array of FileObj objects.
   *
   * @param array $meta_objs Array of FileObj objects
   */
  function set_file_objs($file_objs) {
    $this->file_objs = $file_objs;
    $this->update_files = 1;
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
    if ($thumb_used) {
      $this->thumb_used = "/" . trim($thumb_used, "/");
    }
    else {
      $this->thumb_used = "";
    }
  }
  
  /**
   * Get an array of Chapter objects.
   *
   * @return array of Chapter objects
   */
  function get_chapters() {
    return $this->chapters;
  }

  /**
   * Add a single Chapter.
   *
   * @param Chapter $chapter
   */
  function add_chapter($offset, $title = null, $description = null) {
    array_push($this->chapters, new Chapter($offset, $title, $description));
  }

  /**
   * Add an array of Chapter objects.
   *
   * @param array $chapters Array of Chapter objects
   */
  function add_chapters($chapters) {
    array_merge($this->chapters, $chapters);
  }

  /**
   * Set all chapters (replaces existing ones).
   *
   * @param array $chapters Array of Chapter objects
   */
  function set_chapters($chapters) {
    $this->chapters = $chapters;
  }


  /**
   * Get an array of Comment objects.
   *
   * @return array of Comment objects
   */
  function get_comments() {
    return $this->comments;
  }

  /**
   * Add a single Comment.
   *
   * @param Comment $comment
   */
  function add_comment($offset, $title = null, $description = null, $author = null, $type = null, $updated = null, $published = null) {
    array_push($this->comments, new Comment($offset, $title, $description, $author, $type, $updated, $published));
  }

  /**
   * Add an array of Comment objects.
   *
   * @param array $comments Array of Comment objects
   */
  function add_comments($comments) {
    array_merge($this->comments, $comments);
  }

  /**
   * Set all comments (replaces existing ones).
   *
   * @param array $comments Array of Comment objects
   */
  function set_comments($comments) {
    $this->comments = $comments;
  }
  
  
  /**
   * Fill up this object (will be cleared first) with data from an stdClass object (which is the result of json decoding a 'content' entry)
   *
   * @param stdClass Object corresponding to a 'content' entry (json decoded response from the META service).
   */
  function from_entry($entry)
  {
    $this->clear();
    # get the inner entry obj
    $inner_entry = null;
    if (property_exists($entry, "entry")) { # the $entry object may look like {"entry":{"id":"xxx","content":{}}}
      $inner_entry = $entry->entry;
    }
    elseif (property_exists($entry, "id")) { # the $entry object may also look like {"id":"xxx","content",{},...}
      $inner_entry = $entry;
    }
    if (! $inner_entry) {
      throw new Exception("MetaContent:from_entry() : invalid argument entry passed");
    }

    $this->id = $inner_entry->id;
    $this->name = $inner_entry->content->params->name;
    $this->meta_updated = $inner_entry->content->params->meta_updated;
    $this->yt_id = $inner_entry->content->params->yt_id;
    $this->thumb_used = $inner_entry->content->file_params->thumb_used;
    $this->update_files = $inner_entry->content->file_params->update_files;
    # set tags
    if (property_exists($inner_entry->content->params, "tag")) {
      foreach ($inner_entry->content->params->tag as $t) {
        array_push($this->tags, $t);
      }
    }
    # set meta objs
    if (property_exists($inner_entry->content->params, "meta")) {
      foreach ($inner_entry->content->params->meta as $m) {
        $text = "";
        if (property_exists($m, "text")) {
          $text = $m->text;
        }
        $lang = "";
        if (property_exists($m, "lang")) {
          $lang = $m->lang;
        }
        array_push($this->meta_objs, new MetaObj($m->meta_name, $m->vocab, $text, $lang, get_object_vars($m)));
      }
    }
    # set files
    foreach ($inner_entry->content->file as $f) {
      array_push($this->file_objs, new FileObj($f->path, $f->media_type, $f->size, $f->duration, $f->container, $f->bitrate, $f->width, $f->height, $f->frames, $f->framerate, $f->samplerate, $f->filename, $f->extension, $f->mimetype, $f->download, $f->rtmp_streamer, $f->rtmp_location, $f->rtsp, $f->apple_http, $f->silverlight, $f->adobe_http, $f->rtmp_adaptive, $f->language));
    }
    # set chapters
    if (property_exists($inner_entry->content, "chapter")) {
      foreach ($inner_entry->content->chapter as $ch) {
        array_push($this->chapters, new Chapter($ch->offset, $ch->title, $ch->description, $ch->id));
      }
    }
    # set comments
    if (property_exists($inner_entry->content, "comment")) {
      foreach ($inner_entry->content->comment as $ch) {
        array_push($this->comments, new Comment($ch->offset, $ch->title, $ch->description, $ch->author, $ch->type, $ch->updated, $ch->published, $ch->id, $ch->publish_time, $ch->insert_time));
      }
    }
  }

  /**
   * Store the object's datamembers in an array corresponding to a 'content' entry (ready for encoding into json)
   *
   * @return array Associative array (key, value are both strings).
   */
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
    # set chapters
    $entry["entry"]["content"]["chapter"] = array();
    foreach ($this->chapters as $ch) {
      array_push($entry["entry"]["content"]["chapter"], $ch->to_array());
    }
    # set comments
    $entry["entry"]["content"]["comment"] = array();
    foreach ($this->comments as $ch) {
      array_push($entry["entry"]["content"]["comment"], $ch->to_array());
    }

    return $entry;
  }
}


?>