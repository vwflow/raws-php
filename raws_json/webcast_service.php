<?php
# Copyright (C) 2012 rambla.eu
# DISCLAIMER: This functionality has not yet been released, this script is for development purposes only !
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
 * @file Client for communication with the META web service.
 *
 * @see https://wiki.rambla.be/META_REST_API
 * @package	raws-php
 * @copyright rambla.eu, 2012
 * @version 1.0 (2012/10/10)
 */

require_once dirname(__FILE__) . '/json_service.php';

/**
 * Client for REST communication with the META service, using json as the data format.
 *
 * @see https://wiki.rambla.be/META_REST_API
 */
class WebcastService extends JsonService
{

  /**
   * Constructor.
   *
   * @param string $username Rambla user account name
   * @param string $password Rambla user account pwd
   * @param string $server Name of the web-service (optional).
   * @param bool $ssl Set to True if you're using SSL (default = False, if you want to use SSL for file uploads make sure you have a 'secure' user account - contact support@rambla.be)
   * @param string $user_agent Name of the user agent (is passed in the 'User-Agent' HTTP header).
   */
	function __construct($username, $password, $server = null, $ssl = False, $user_agent = "raws-php") 
	{
    $this->server = "meta.meta01.rambla.be";
    if ($server) {
      $this->server = $server;
    }
    parent::__construct($username, $password, $this->server, $ssl, $user_agent);
	}
	

  # Webcast Methods
  # -------------

  /**
   * Create a new webcast instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param string $status Should be 'pre-live', 'empty', 'live', 'vod' or 'vod-local'.
   * @param string $title Webcast title.
   * @param string $description Webcast description.
   * @param string $owner Webcast owner name.
   * @param array $resolutions Array of resolutions, allowed values are: "240p", "360p", "480p", "720p".
   * @param array $wchannels Channel(s) to which this webcast belong(s). Each channel should be an associative array with (at least) an "id" element.
   * @param string $speaker Speaker info
   * @param string $agenda Webcast agenda
   * @param string $date UNIX timestamp.
   * @return stdClass Object corresponding to the webcast instance that has been created.
   * @see https://wiki.rambla.be/META_webcast_resource#POST
   */
	function createWebcast($status, $title = null, $description = null, $owner = null, $resolutions = null, $wchannels = null, $speaker = null, $agenda = null, $date = null, $post_response = True, $recording_type = null, $auto_publish = null, $email_to = null, $registration_required = null, $event_start_time = null)
	{
	  $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["status"] = $status;
    if ($title) { $v["entry"]["content"]["params"]["title"] = $title;}
    if ($description) { $v["entry"]["content"]["params"]["description"] = $description;}
    if ($owner) { $v["entry"]["content"]["params"]["owner"] = $owner;}
    if ($speaker) { $v["entry"]["content"]["params"]["speaker"] = $speaker;}
    if ($agenda) { $v["entry"]["content"]["params"]["agenda"] = $agenda;}
    if ($date) { $v["entry"]["content"]["params"]["date"] = $date;}
    if ($resolutions) { 
      $v["entry"]["content"]["params"]["resolutions"] = array();
      $v["entry"]["content"]["params"]["resolutions"] = $resolutions;
    }
    if($wchannels) {
      $v["entry"]["content"]["action"]["update_wchannel"] = "1";
      $v["entry"]["content"]["wchannel"] = $wchannels;
    }
    if (is_bool($post_response)) {
      $v["entry"]["content"]["actions"] = array();
      $v["entry"]["content"]["actions"]["post_response"] = (int)$post_response;
    }
    if ($recording_type) { $v["entry"]["content"]["params"]["recording_type"] = $recording_type;}
    if ($auto_publish) { $v["entry"]["content"]["params"]["auto_publish"] = $auto_publish;}
    if ($email_to) { $v["entry"]["content"]["params"]["email_to"] = $email_to;}
    if ($registration_required) { $v["entry"]["content"]["params"]["registration_required"] = $registration_required;}
    if ($event_start_time) { $v["entry"]["content"]["params"]["event_start_time"] = $event_start_time;}
    
	  $uri = "/webcast/" . $this->username . "/";
    return $this->json_client->POST($uri, $v);
	}

  /**
   * Get a list of webcast objects.
   *
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a webcast feed.
   * @see https://wiki.rambla.be/META_webcast_resource#GET
   */
	function getWebcastList($querystr = null)
	{
    $uri = "/webcast/" . $this->username . "/";
    return $this->json_client->GET($uri, $querystr);
	}

  /**
   * Get a single webcast instance.
   *
   * @param string $id ID that uniquely identifies the webcast instance.
   * @return stdClass Object corresponding to a webcast entry.
   * @see https://wiki.rambla.be/META_webcast_resource#GET
   */
	function getWebcastInstance($id)
	{
    $uri = "/webcast/" . $this->username . "/" . $id . "/";
    return $this->json_client->GET($uri);
	}
	
  /**
   * Update an existing webcast instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $webcast Object corresponding to an existing webcast instance
   * @param string $id ID of the webcast instance to be updated (optional, otherwise the name from the $webcast object is used)
   * @return stdClass Object corresponding to the webcast instance that has been updated.
   * @see https://wiki.rambla.be/META_webcast_resource#POST
   */
	function updateWebcast($webcast, $id = null, $post_response = True)
	{
    $uri = null;
    
    # add post_response to the instance
    if (is_bool($post_response)) 
    {
      if (is_array($webcast)) {
        if (! array_key_exists('action', $entry["entry"]["content"])) {
          $entry["entry"]["content"]["action"] = array();
        }
  	    $entry["entry"]["content"]["action"]["post_response"] = (int)$post_response;
  	  }
  	  elseif (is_object($webcast)) {
  	    if (! isset($webcast->entry->content->action)) {
  	      $webcast->entry->content->action = new stdClass;
	      }
	      $webcast->entry->content->action->post_response = (int)$post_response;
  	  }
	  }
    
	  if ($id) {
  	  $uri = "/webcast/" . $this->username . "/" . $id . "/";
	  }
	  else {
	    $uri = $webcast->entry->id;
	  }
    return $this->json_client->POST($uri, $webcast);
	}

  /**
   * Delete a webcast instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id ID that uniquely identifies the webcast instance.
   * @param bool $delete_content If True, any content instance attached to the webcast will also be deleted.
   * @see https://wiki.rambla.be/META_webcast_resource#DELETE
   */
	function deleteWebcast($id, $delete_content = False)
	{
	  $uri = "/webcast/" . $this->username . "/" . $id . "/";
	  $querystr = "delete_content=0";
	  if ($delete_content) {
  	  $querystr = "delete_content=1";
	  }
    return $this->json_client->DELETE($uri, $querystr);
	}
  
  /**
   * Checks if a webcast with a given name exists.
   *
   * @param string $id ID that uniquely identifies the webcast instance.
   * @return bool True if exists.
   */
  function webcastExists($id) 
  {
    $exists = False;
    $uri = "/webcast/" . $this->username . "/" . $id . "/";
    try {
      $this->json_client->GET($uri);
      $exists = True;
    }
    catch(RawsRequestException $e) {
      $exists = False;
    }
    return $exists;
  }
  
  
  function trimWebcast($id, $trim_timestamp, $trim_start_secs, $trim_end_secs, $path, $resolution) 
  {
    $a = array();
    $a["webcast_id"] = $id;
    $a["trim_timestamp"] = $trim_timestamp;
    $a["trim_start_secs"] = $trim_start_secs;
    $a["trim_end_secs"] = $trim_end_secs;
    $a["path"] = $path;
    $a["resolution"] = $resolution;
    $uri = "/webcast/trim/" . $this->username . "/" . $id . "/";
    return $this->json_client->POST($uri, $a);
  }
  
  function revertWebcast($id) 
  {
    $a = array();
    $a["webcast_id"] = $id;
    $uri = "/webcast/revert/" . $this->username . "/" . $id . "/";
    return $this->json_client->POST($uri, $a);
  }
  
  # Wchannel Methods
  # -------------

  /**
   * Create a new wchannel instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param string $title Wchannel title.
   * @param string $description Wchannel description.
   * @param string $webcast_id ID of a single webcast instance (alternative to setting $webcasts argument)
   * @param array $webcasts Channel(s) to which this wchannel belong(s). Each channel should be an associative array with (at least) an "id" element.
   * @return stdClass Object corresponding to the wchannel instance that has been created.
   * @see https://wiki.rambla.be/META_wchannel_resource#POST
   */
	function createWchannel($title, $description, $webcast_id = null, $webcasts = null)
	{
	  $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["title"] = $title;
    $v["entry"]["content"]["params"]["description"] = $description;
    if ($webcast_id) {
      $v["entry"]["content"]["webcast"] = array();
      $v["entry"]["content"]["webcast"][] = array("id" => $webcast_id);
    }
    elseif($webcasts) {
      $v["entry"]["content"]["webcast"] = $webcasts;
    }
    
	  $uri = "/wchannel/" . $this->username . "/";
    return $this->json_client->POST($uri, $v);
	}

  /**
   * Get a list of wchannel objects.
   *
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a wchannel feed.
   * @see https://wiki.rambla.be/META_wchannel_resource#GET
   */
	function getWchannelList($querystr = null)
	{
    $uri = "/wchannel/" . $this->username . "/";
    return $this->json_client->GET($uri, $querystr);
	}

  /**
   * Get a single wchannel instance.
   *
   * @param string $id ID that uniquely identifies the wchannel instance.
   * @return stdClass Object corresponding to a wchannel entry.
   * @see https://wiki.rambla.be/META_wchannel_resource#GET
   */
	function getWchannelInstance($id)
	{
    $uri = "/wchannel/instance/" . $id . "/";
    return $this->json_client->GET($uri);
	}
	
  /**
   * Update an existing wchannel instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $wchannel Object corresponding to an existing wchannel instance
   * @param string $id ID of the wchannel instance to be updated (optional, otherwise the id from the $wchannel object is used)
   * @return stdClass Object corresponding to the wchannel instance that has been updated.
   * @see https://wiki.rambla.be/META_wchannel_resource#POST
   */
	function updateWchannel($wchannel, $id = null)
	{
    $uri = null;
	  if ($id) {
  	  $uri = "/wchannel/instance/" . $id . "/";
	  }
	  else {
	    $uri = $wchannel->entry->id;
	  }
    return $this->json_client->POST($uri, $wchannel);
	}

  /**
   * Delete a wchannel instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id ID that uniquely identifies the wchannel instance.
   * @see https://wiki.rambla.be/META_wchannel_resource#DELETE
   */
	function deleteWchannel($id)
	{
	  $uri = "/wchannel/instance/" . $id . "/";
    return $this->json_client->DELETE($uri);
	}
  
  /**
   * Checks if a wchannel with a given name exists.
   *
   * @param string $id ID that uniquely identifies the wchannel instance.
   * @return bool True if exists.
   */
  function wchannelExists($id) 
  {
    $exists = False;
    $uri = "/wchannel/instance/" . $id . "/";
    try {
      $this->json_client->GET($uri);
      $exists = True;
    }
    catch(RawsRequestException $e) {
      $exists = False;
    }
    return $exists;
  }
  
  # Wslide methods
  # --------------
  
  /**
   * Create a new wslide instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param string $webcast_id Webcast ID
   * @param string $path Relative path to the slide img.
   * @param string $timestamp Time the slide was taken
   * @param array $offset Offset from the beginning of the webcast.
   * @return stdClass Object corresponding to the wslide instance that has been created.
   * @see https://wiki.rambla.be/META_wslide_resource#POST
   */
  function createWslide($webcast_id, $path, $timestamp, $offset, $width = null, $height = null)
  {
    $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["webcast_id"] = $webcast_id;
    $v["entry"]["content"]["params"]["path"] = $path;
    $v["entry"]["content"]["params"]["timestamp"] = $timestamp;
    $v["entry"]["content"]["params"]["offset"] = $offset;
    $v["entry"]["content"]["params"]["width"] = $width;
    $v["entry"]["content"]["params"]["height"] = $height;

    $uri = "/wslide/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->POST($uri, $v);
  }

  /**
   * Create a new wslide instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param string $webcast_id Webcast ID
   * @param string $path Relative path to the slide img.
   * @param string $timestamp Time the slide was taken
   * @param array $offset Offset from the beginning of the webcast.
   * @return stdClass Object corresponding to the wslide instance that has been created.
   * @see https://wiki.rambla.be/META_wslide_resource#POST
   */
  function createWslideTmp($webcast_id, $path, $width, $height)
  {
    $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["webcast_id"] = $webcast_id;
    $v["entry"]["content"]["params"]["path"] = $path;
    $v["entry"]["content"]["params"]["width"] = $width;
    $v["entry"]["content"]["params"]["height"] = $height;

    $uri = "/wslide/tmp/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->POST($uri, $v);
  }
  
  /**
   * Get a list of wslide objects.
   *
   * @param string $webcast_id Webcast identifier
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a wslide feed.
   * @see https://wiki.rambla.be/META_wslide_resource#GET
   */
  function getWslideList($webcast_id, $querystr = null)
  {
    $uri = "/wslide/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->GET($uri, $querystr);
  }

  /**
   * Get a single wslide instance.
   *
   * @param string $id Wslide identifier
   * @return stdClass Object corresponding to a wslide entry.
   * @see https://wiki.rambla.be/META_wslide_resource
   */
  function getWslide($id)
  {
    $uri = "/wslide/instance/" . $id . "/";
    return $this->json_client->GET($uri);
  }

  /**
   * Update an existing wslide instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $wslide Object corresponding to an existing wslide instance
   * @return stdClass Object corresponding to the wslide instance that has been updated.
   * @see https://wiki.rambla.be/META_wslide_resource
   */
	function updateWslide($wslide)
	{
    $uri = $wslide->entry->id;
    return $this->json_client->POST($uri, $wslide);
	}


  /**
   * Deletes all Wslide instances linked to a given webcast.
   *
   * @param string $webcast_id Webcast identifier
   * @param string $delete_from_cdn Also delete the file from the CDN.
   * @see https://wiki.rambla.be/META_wslide_resource
   */
  function deleteWslideList($webcast_id, $delete_from_cdn = True)
  {
    $querystr = "delete_from_cdn=1";
    if (! $delete_from_cdn) {
      $querystr = "delete_from_cdn=0";
    }
    $uri = "/wslide/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->DELETE($uri, $querystr);
  }


  /**
   * Delete a wslide instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id Slide id.
   * @param string $delete_from_cdn Also delete the file from the CDN.
   * @see https://wiki.rambla.be/META_wslide_resource
   */
  function deleteWslide($id, $delete_from_cdn = True)
  {
    $querystr = "delete_from_cdn=1";
    if (! $delete_from_cdn) {
      $querystr = "delete_from_cdn=0";
    }
    $uri = "/wslide/instance/" . $id . "/";
    return $this->json_client->DELETE($uri, $querystr);
  }
  
  /**
   * Add a single asset to the webcast.
   *
   * @param string $webcast_id ID of an existing webcast instance.
   * @param string $content_name Name of a content instance that will be added as asset.
   * @return stdClass $webcast Updated webcast instance.
   */
  function addAssetToWebcast($webcast_id, $content_name, $type)
  {
	  $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"]["asset"] = $content_name;
    $v["entry"]["content"]["params"]["type"] = $type;
    
	  $uri = "/webcast/add_asset/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->POST($uri, $v);
  }
  
  /**
   * Remove a single asset from the webcast.
   *
   * @param string $webcast_id ID of the webcast instance to which the asset belongs.
   * @param string $asset_name Content name of the asset to be removed.
   * @return stdClass $webcast Updated webcast instance.
   */
  function rmAssetFromWebcast($webcast_id, $asset_name)
  {
	  $uri = "/webcast/rm_asset/" . $this->username . "/" . $webcast_id . "/"  . $asset_name . "/";
    return $this->json_client->DELETE($uri);
  }

  /**
   * Remove assets from the webcast.
   *
   * @param string $webcast_id ID of the webcast instance to which the asset belongs.
   * @param string $types String containing single or multiple (comma-separated) asset types marked for deletion. 
   *                      Leave empty to delete all assets from the webcast.
   * @return stdClass $webcast Updated webcast instance.
   */
  function rmAssetsFromWebcast($webcast_id, $types)
  {
	  $uri = "/webcast/rm_assets/" . $this->username . "/" . $webcast_id . "/";
	  $querystr = "";
	  if ($types) {
  	  $querystr = "type=" . $types;
	  }
    return $this->json_client->DELETE($uri, $querystr);
  }
  
  ## COMMENTS
  
  /**
   * Get a list of comment instances.
   *
   * @param string $content_name Name of the content instance to which the comment should be attached (= required).
   * @return stdClass Object corresponding to a comment feed.
   * @see https://wiki.rambla.be/META_comment_resource
   */
	function getCommentList($webcast_id, $time_from = null, $time_to = null, $published = null, $min_offset = null, $type = null, $notype = null, $order_by = null, $speaker_viewing = null)
	{
    $uri = "/comments/"  . $this->username . "/" . $webcast_id . "/";
    $qstr = "";
    if ($time_from !== null) {
      $qstr .= "time_from=" . $time_from;
    }
    if ($time_to !== null) {
      if ($qstr) {
        $qstr .= ";";
      }
      $qstr .= "time_to=" . $time_to;
    }
    if ($published !== null) {
      if ($qstr) {
        $qstr .= ";";
      }
      $qstr .= "published=" . $published;
    }
    if ($min_offset !== null) {
      if ($qstr) {
        $qstr .= ";";
      }
      $qstr .= "min_offset=" . $min_offset;
    }
    if ($type) {
      if ($qstr) {
        $qstr .= ";";
      }
      $qstr .= "type=" . $type;
    }
    if ($notype) {
      if ($qstr) {
        $qstr .= ";";
      }
      $qstr .= "notype=" . $notype;
    }
    if ($order_by) {
      if ($qstr) {
        $qstr .= ";";
      }
      $qstr .= "order_by=" . $order_by;
    }
    if ($speaker_viewing !== null) {
      if ($qstr) {
        $qstr .= ";";
      }
      $qstr .= "speaker_viewing=" . $speaker_viewing;
    }
    
    return $this->json_client->GET($uri, $qstr);
	}

  /**
   * Get a comment key.
   *
   * @return stdClass Object corresponding to a comment key.
   */
	function getCommentViewer($webcast_id)
	{
    $uri = "/wcviewer/"  . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->GET($uri);
	}
  
  /**
   * Create a new comment instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param string $content_name Name of the content instance to which the comment should be attached (= required).
   * @param string $xml_namespace URL of the (XML) namespace (e.g. http://purl.org/dc/elements/1.1/) (= required)
   * @return stdClass Object corresponding to the vocab instance that has been created.
   * @see https://wiki.rambla.be/META_comment_resource
   */
	function createComment($webcast_id, $title, $description, $author, $type, $auth_key_id = null, $auth_key = null, $publish_time = null, $insert_time = null, $speaker_viewing = null)
	{
	  $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["title"] = $title;
    $v["entry"]["content"]["params"]["description"] = $description;
    $v["entry"]["content"]["params"]["author"] = $author;
    $v["entry"]["content"]["params"]["type"] = $type;
    if ($publish_time) {
      $v["entry"]["content"]["params"]["publish_time"] = $publish_time;
    }
    if ($insert_time) {
      $v["entry"]["content"]["params"]["insert_time"] = $insert_time;
    }
    if ($speaker_viewing) {
      $v["entry"]["content"]["params"]["speaker_viewing"] = $speaker_viewing;
    }

    $v["entry"]["auth"] = array();
    $v["entry"]["auth"]["key_id"] = $auth_key_id;
    $timestamp = time();
    $v["entry"]["auth"]["timestamp"] = $timestamp;
    $v["entry"]["auth"]["hmac"] = md5($auth_key.$timestamp);
     
	  $uri = "/comments/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->POST($uri, $v);
	}

  /**
   * Create a new comment instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param string $content_name Name of the content instance to which the comment should be attached (= required).
   * @param string $xml_namespace URL of the (XML) namespace (e.g. http://purl.org/dc/elements/1.1/) (= required)
   * @return stdClass Object corresponding to the vocab instance that has been created.
   * @see https://wiki.rambla.be/META_comment_resource
   */
	function createAdminComment($webcast_id, $title, $description, $author, $type, $publish_time = null, $insert_time = null, $speaker_viewing = null)
	{
	  $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["title"] = $title;
    $v["entry"]["content"]["params"]["description"] = $description;
    $v["entry"]["content"]["params"]["author"] = $author;
    $v["entry"]["content"]["params"]["type"] = $type;
    $v["entry"]["content"]["params"]["auto_publish"] = 1;
    if ($publish_time) {
      $v["entry"]["content"]["params"]["publish_time"] = $publish_time;
    }
    if ($insert_time) {
      $v["entry"]["content"]["params"]["insert_time"] = $insert_time;
    }
    if ($speaker_viewing) {
      $v["entry"]["content"]["params"]["speaker_viewing"] = $speaker_viewing;
    }
     
	  $uri = "/comments/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->POST($uri, $v);
	}
	  
  /**
   * Get a single comment instance.
   *
   * @param string $id ID that uniquely identifies the comment instance.
   * @return stdClass Object corresponding to a comment entry.
   * @see https://wiki.rambla.be/META_comment_resource#GET
   */
	function getCommentInstance($id)
	{
    $uri = "/comment/"  . $this->username . "/" . $id . "/";
    return $this->json_client->GET($uri);
	}
	
  /**
   * Delete a comment instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id Slide id.
   * @see https://wiki.rambla.be/META_comment_resource#DELETE
   */
  function deleteComment($id)
  {
    $uri = "/comment/"  . $this->username . "/" . $id . "/";
    return $this->json_client->DELETE($uri);
  }
  
  /**
   * Update an existing comment instance.
   *
   * @param array $params Params for this comment entry
   * @return stdClass Object corresponding to the comment instance that has been created.
   * @see https://wiki.rambla.be/META_comment_resource#POST
   */
  function updateComment($params)
  {
    if (is_object($params)) {
      $params = get_object_vars($params);
    }
    if (! $params["id"]) {
      throw new Exception("updateComment() : params argument must contain the comment id");
    }
    $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = $params;
    
    $uri = "/comment/"  . $this->username . "/" . $params["id"] . "/";
    return $this->json_client->POST($uri, $v);
  }
  
  # wcuser methods
  # --------------
  
  function updateWcuser($username, $live_hours_allowed, $concurrent_live_viewers_allowed, $concurrent_live_streams_allowed, $vod_webcasts_allowed, $vod_bandwidth_allowed, $resolutions_allowed, $aspect_ratio = null, $port = null)
  {
    $e = $this->json_client->get_empty_entry_array();
    $e["entry"]["content"]["params"]["live_hours_allowed"] = $live_hours_allowed;
    $e["entry"]["content"]["params"]["concurrent_live_viewers_allowed"] = $concurrent_live_viewers_allowed;
    $e["entry"]["content"]["params"]["concurrent_live_streams_allowed"] = $concurrent_live_streams_allowed;
    $e["entry"]["content"]["params"]["vod_webcasts_allowed"] = $vod_webcasts_allowed;
    $e["entry"]["content"]["params"]["vod_bandwidth_allowed"] = $vod_bandwidth_allowed;
    $e["entry"]["content"]["params"]["resolutions_allowed"] = $resolutions_allowed;
    $e["entry"]["content"]["params"]["aspect_ratio"] = $aspect_ratio;
    $e["entry"]["content"]["params"]["port"] = $port;

    $uri = "/wcuser/" . $username . "/";
    return $this->json_client->POST($uri, $e);
  }
 
  function getWcuser($username = null)
  {
    if (!$username) {
      $username = $this->username;
    }
    $uri = "/wcuser/" . $username . "/";
    return $this->json_client->GET($uri);
  }
  
  function deleteWcuser($username)
  {
    $uri = "/wcuser/" . $username . "/";
    return $this->json_client->DELETE($uri);
  }

  function send_mail($from, $to, $subject, $text_content, $html_content)
  {
     $e = $this->json_client->get_empty_entry_array();
     $e["entry"]["content"]["params"]["from"] = $from;
     $e["entry"]["content"]["params"]["to"] = $to;
     $e["entry"]["content"]["params"]["subject"] = $subject;
     $e["entry"]["content"]["params"]["text_content"] = $text_content;
     $e["entry"]["content"]["params"]["html_content"] = $html_content;

     $uri = "/mail/" . $this->username . "/";
     return $this->json_client->POST($uri, $e);
  }

  # utility functions
  # -----------------
  
  /**
   * Sets the record_start of the webcast.
   *
   * @param stdClass $webcast Existing webcast instance.
   * @param int $timestamp UNIX timestamp to be set in the webcast's record_start param (if null, the current time is used).
   * @return stdClass $webcast Updated webcast instance.
   */
  function webcastSetRecordStart($webcast, $timestamp = null) 
  {
    $uri = "/wc/record/start/" . $this->username . "/" . $webcast->entry->content->params->id . "/";
    $querystr = "";
    if ($timestamp) {
      $querystr = "?timestamp=$timestamp";
    }
    if ($webcast->entry->content->params->recording_type) {
      if ($querystr) {
        $querystr = $querystr . ";recording_type=" . $webcast->entry->content->params->recording_type;
      }
      else {
        $querystr = "?recording_type=" . $webcast->entry->content->params->recording_type;
      }
    }
    return $this->json_client->GET($uri, $querystr);
  }

  /**
   * Sets the record_end of the webcast.
   *
   * @param stdClass $webcast Existing webcast instance.
   * @param int $timestamp UNIX timestamp to be set in the webcast's record_end param (if null, the current time is used).
   * @return stdClass $webcast Updated webcast instance.
   */
  function webcastSetRecordEnd($webcast, $timestamp = null) 
  {
    $uri = "/wc/record/end/" . $this->username . "/" . $webcast->entry->content->params->id . "/";
    $querystr = "";
    if ($timestamp) {
      $querystr = "?timestamp=$timestamp";
    }
    return $this->json_client->GET($uri, $querystr);
  }

  /**
   * Sets the broadcast_start of the webcast.
   *
   * @param stdClass $webcast Existing webcast instance.
   * @param int $timestamp UNIX timestamp to be set in the webcast's broadcast_start param (if null, the current time is used).
   * @return stdClass $webcast Updated webcast instance.
   */
  function webcastSetBroadcastStart($webcast, $timestamp = null) 
  {
    $uri = "/wc/broadcast/start/" . $this->username . "/" . $webcast->entry->content->params->id . "/";
    $querystr = "";
    if ($timestamp) {
      $querystr = "?timestamp=$timestamp";
    }
    return $this->json_client->GET($uri, $querystr);
  }

  /**
   * Sets the broadcast_end of the webcast.
   *
   * @param stdClass $webcast Existing webcast instance.
   * @param int $timestamp UNIX timestamp to be set in the webcast's broadcast_end param (if null, the current time is used).
   * @return stdClass $webcast Updated webcast instance.
   */
  function webcastSetBroadcastEnd($webcast, $timestamp = null) 
  {
    $uri = "/wc/broadcast/end/" . $this->username . "/" . $webcast->entry->content->params->id . "/";
    $querystr = "";
    if ($timestamp) {
      $querystr = "?timestamp=$timestamp";
    }
    return $this->json_client->GET($uri, $querystr);
  }
  
  /**
   * Sets the event status of the webcast.
   *
   * @param stdClass $webcast Existing webcast instance.
   * @param int $status_id (0 = waiting, 1 = started, 2 = paused, 3 = ended)
   * @return stdClass $webcast Updated webcast instance.
   */
  function webcastSetEventStatus($webcast, $status_id) 
  {
    $uri = "/wc/event/switch_status/" . $this->username . "/" . $webcast->entry->content->params->id . "/";
    $querystr = "?new_status=$status_id";
    return $this->json_client->GET($uri, $querystr);
  }

  
  /**
   * Sends a request with the delete_recording action set.
   *
   * @param stdClass $webcast Existing webcast instance.
   * @return stdClass $webcast Updated webcast instance.
   */
  function webcastDeleteRecording($webcast) 
  {
    $uri = "/wc/record/delete/" . $this->username . "/" . $webcast->entry->content->params->id . "/";
    return $this->json_client->GET($uri);
    
    // $webcast->entry->content->action = new stdClass;
    // $webcast->entry->content->action->delete_recording = True;
    // return $this->updateWebcast($webcast);
  }
  
  
  /**
   * Get the name of the content instance linked to this webcast
   *
   * @return string Containing the name of the content instance linked to this webcast.
   */
  function getWebcastContentName($webcast)
  {
    $content_name = null;
    foreach($webcast->entry->content->content as $c) {
      $content_name = $c->name;
    } 
    return $content_name;
  }
  
  # Registrant methods
  # --------------
  
  /**
   * Create or modify a registrant instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param string $webcast_id Webcast ID
   * @param string $email Email address
   * @param string $secret Registration code (should be set when creating the registrant)
   * @param string $status Registration status (optional, default = 'ok')
   * @param string $viewed Viewing status (optional, set to "0" or "1", default = "0")
   * @return stdClass Object corresponding to the registrant instance that has been created.
   * @see https://wiki.rambla.be/META_registrant_resource#POST
   */
  function updateRegistrant($webcast_id, $email, $secret, $status = null, $viewed = null)
  {
    $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["webcast_id"] = $webcast_id;
    $v["entry"]["content"]["params"]["email"] = $email;
    $v["entry"]["content"]["params"]["secret"] = $secret;
    $v["entry"]["content"]["params"]["status"] = $status;
    $v["entry"]["content"]["params"]["viewed"] = $viewed;

    $uri = "/registrant/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->POST($uri, $v);
  }

  /**
   * Get a list of all registrant entries for a given webcast.
   *
   * @param string $webcast_id Webcast identifier
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a registrant feed.
   * @see https://wiki.rambla.be/META_registrant_resource#GET
   */
  function getRegistrantList($webcast_id, $querystr = null)
  {
    $uri = "/registrant/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->GET($uri, $querystr);
  }
  
  /**
   * Delete one or more registrants for a given webcast.
   *
   * If the $email argument is passed, only the registrant with the given email address is deleted.
   * If $email = null, all registrant for the given webcast will be deleted. 
   *
   * @param string $webcast_id Webcast identifier
   * @param string $email Email address of the registrant that should be deleted.
   * @see https://wiki.rambla.be/META_registrant_resource#GET
   */
  function deleteRegistrants($webcast_id, $email = null)
  {
    $uri = "/registrant/" . $this->username . "/" . $webcast_id . "/";
    $querystr = "";
    if ($email) {
      $querystr = "?email=" . $email;
    }
    return $this->json_client->DELETE($uri, $querystr);
  }

  /**
   * Get a single registrant instance.
   *
   * @param string $id Registrant identifier
   * @return stdClass Object corresponding to a registrant entry.
   * @see https://wiki.rambla.be/META_registrant_resource
   */
  function getRegistrantById($registrant)
  {
    $uri = "/registrant/" . $this->username . "/" . $registrant->entry->content->params->webcast_id . "/" . $registrant->entry->content->params->id . "/";
    return $this->json_client->GET($uri);
  }

  /**
   * Update an existing registrant instance (allows changing the email address of an existing registrant).
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $registrant Object corresponding to an existing registrant instance
   * @return stdClass Object corresponding to the registrant instance that has been updated.
   * @see https://wiki.rambla.be/META_registrant_resource
   */
	function updateRegistrantById($registrant)
	{
    $uri = "/registrant/" . $this->username . "/" . $registrant->entry->content->params->webcast_id . "/" . $registrant->entry->content->params->id . "/";
    return $this->json_client->POST($uri, $registrant);
	}

  /**
   * Delete a registrant instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id Slide id.
   * @param string $delete_from_cdn Also delete the file from the CDN.
   * @see https://wiki.rambla.be/META_registrant_resource
   */
  function deleteRegistrantById($registrant)
  {
    $uri = "/registrant/" . $this->username . "/" . $registrant->entry->content->params->webcast_id . "/" . $registrant->entry->content->params->id . "/";
    return $this->json_client->DELETE($uri);
  }


  /**
   * Check if a registrant has access.
   *
   * @param string $id Registrant identifier
   * @return stdClass Object corresponding to a webcast entry (or Excepion is raised if access was refused).
   * @see https://wiki.rambla.be/META_registrant_resource
   */
  function hasWebcastAccess($webcast_id, $email, $secret)
  {
    $uri = "/wcaccess/" . $this->username . "/" . $webcast_id . "/";
    $qstr = "?email=$email;secret=$secret";
    return $this->json_client->GET($uri, $qstr);
  }

  /**
   * Get a single event instance.
   *
   * @param string $id ID that uniquely identifies the webcast/event instance.
   * @return stdClass Object corresponding to a event entry.
   * @see https://wiki.rambla.be/META_event_resource#GET
   */
	function getWcEventInstance($id)
	{
    $uri = "/wcevent/" . $this->username . "/" . $id . "/";
    return $this->json_client->GET($uri);
	}
	
	
  /**
   * Update an existing event instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $event Object corresponding to an existing event instance
   * @return stdClass Object corresponding to the event instance that has been updated.
   * @see https://wiki.rambla.be/META_event_resource#POST
   */
	function updateWcEvent($event)
	{
	  $uri = "/wcevent/" . $this->username . "/" . $event->entry->content->params->id . "/";
    return $this->json_client->POST($uri, $event);
	}

  /**
   * Update an existing event instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param string $id ID that uniquely identifies the webcast/event instance.
   * @return stdClass Object corresponding to a event entry.
   * @see https://wiki.rambla.be/META_event_resource
   */
	function setWebcastEventProperties($id, $general_img, $waiting_img = null, $paused_img = null, $ended_img = null, $waiting_msg = null, $paused_msg = null, $ended_msg = null, $start_time = null, $end_time = null)
	{
    $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    if ($general_img) { $v["entry"]["content"]["params"]["general_img"] = $general_img;}
    if ($waiting_img) { $v["entry"]["content"]["params"]["waiting_img"] = $waiting_img;}
    if ($paused_img) { $v["entry"]["content"]["params"]["paused_img"] = $paused_img;}
    if ($ended_img) { $v["entry"]["content"]["params"]["ended_img"] = $ended_img;}
    if ($waiting_msg) { $v["entry"]["content"]["params"]["waiting_msg"] = $waiting_msg;}
    if ($paused_msg) { $v["entry"]["content"]["params"]["paused_msg"] = $paused_msg;}
    if ($ended_msg) { $v["entry"]["content"]["params"]["ended_msg"] = $ended_msg;}
    if ($start_time) { $v["entry"]["content"]["params"]["start_time"] = $start_time;}
    if ($end_time) { $v["entry"]["content"]["params"]["end_time"] = $end_time;}
	  $uri = "/wcevent/" . $this->username . "/" . $id . "/";
    return $this->json_client->POST($uri, $v);	
  }

  /**
   * Poll event instance.
   *
   * @param string $id ID that uniquely identifies the webcast/event instance.
   * @return stdClass Object corresponding to a event poll entry.
   * @see https://wiki.rambla.be/META_event_resource#GET
   */
	function pollEvent($id)
	{
    $uri = "/poll/wcevent/" . $this->username . "/" . $id . "/";
    return $this->json_client->GET($uri);
	}
	
  /**
   * Replace the video(s) of an existing webcast
   *
   * Throws a RawsRequestException if the instance could not be created.
   * Throws a RawsClientException if the entry argument is invalid.
   *
   * @param mixed $entry Associative array or stdClass object that can be json encoded into a valid content entry.
   * @return stdClass Object corresponding to the webcast entry.
   */
	function replaceWebcastVideo($webcast_id, $content_entry)
	{
	  $uri = "/webcast/video_replace/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->POST($uri, $content_entry);
	}

  /**
   * Replace the video(s) of an existing webcast
   *
   * Throws a RawsRequestException if the instance could not be created.
   * Throws a RawsClientException if the entry argument is invalid.
   *
   */
	function revertToOriginalVideo($webcast_id)
	{
	  $entry = "";
	  $uri = "/webcast/video_revert/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->POST($uri, $entry);
	}

  /**
   * Set the next poll
   *
   * Throws a RawsRequestException if the instance could not be created.
   * Throws a RawsClientException if the entry argument is invalid.
   *
   */
  function setNextPoll($owner, $webcast_id, $poll_id, $question, $expire, $choices) 
  {
    $a = array();
    $a["owner"] = $owner;
    $a["webcast_id"] = $webcast_id;
    $a["poll_id"] = $poll_id;
    $a["question"] = $question;
    $a["expire"] = $expire;
    $a["choices"] = $choices;
    $uri = "/poll/next/" . $this->username . "/" . $webcast_id . "/";
    return $this->json_client->POST($uri, $a);
  }

}
