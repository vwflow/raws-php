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
	function createWebcast($status, $title = null, $description = null, $owner = null, $resolutions = null, $wchannels = null, $speaker = null, $agenda = null, $date = null, $post_response = True)
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
  
  /**
   * Sets the record_start of the webcast.
   *
   * @param stdClass $webcast Existing webcast instance.
   * @param int $timestamp UNIX timestamp to be set in the webcast's record_start param (if null, the current time is used).
   * @return stdClass $webcast Updated webcast instance.
   */
  function webcastSetRecordStart($webcast, $timestamp = null) 
  {
    if (!$timestamp) {
      $timestamp = time();
    }
    $webcast->entry->content->params->record_start = (string)$timestamp;
    return $this->updateWebcast($webcast);
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
    if (!$timestamp) {
      $timestamp = time();
    }
    $webcast->entry->content->params->record_end = (string)$timestamp;
    return $this->updateWebcast($webcast);
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
  function createWslide($webcast_id, $path, $timestamp, $offset)
  {
    $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["webcast_id"] = $webcast_id;
    $v["entry"]["content"]["params"]["path"] = $path;
    $v["entry"]["content"]["params"]["timestamp"] = $timestamp;
    $v["entry"]["content"]["params"]["offset"] = $offset;

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
  function createWslideTmp($webcast_id, $path)
  {
    $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["webcast_id"] = $webcast_id;
    $v["entry"]["content"]["params"]["path"] = $path;

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
   * Deletes all Wslide instances linked to a given webcast.
   *
   * @param string $webcast_id Webcast identifier
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a wslide feed.
   * @see https://wiki.rambla.be/META_wslide_resource#GET
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
   * @see https://wiki.rambla.be/META_wslide_resource#DELETE
   */
  function deleteWslide($id)
  {
    $uri = "/wslide/" . $id . "/";
    return $this->json_client->DELETE($uri);
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
  
  # wcuser methods
  # --------------
  
  function updateWcuser($username, $live_hours_allowed, $concurrent_live_viewers_allowed, $concurrent_live_streams_allowed, $vod_webcasts_allowed, $vod_bandwidth_allowed, $resolutions_allowed)
  {
    $e = $this->json_client->get_empty_entry_array();
    $e["entry"]["content"]["params"]["live_hours_allowed"] = $live_hours_allowed;
    $e["entry"]["content"]["params"]["concurrent_live_viewers_allowed"] = $concurrent_live_viewers_allowed;
    $e["entry"]["content"]["params"]["concurrent_live_streams_allowed"] = $concurrent_live_streams_allowed;
    $e["entry"]["content"]["params"]["vod_webcasts_allowed"] = $vod_webcasts_allowed;
    $e["entry"]["content"]["params"]["vod_bandwidth_allowed"] = $vod_bandwidth_allowed;
    $e["entry"]["content"]["params"]["resolutions_allowed"] = $resolutions_allowed;

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

}
