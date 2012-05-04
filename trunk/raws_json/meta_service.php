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
 * @file Client for communication with the META web service.
 *
 * @see https://wiki.rambla.be/META_REST_API
 * @package	raws-php
 * @copyright rambla.eu, 2012
 * @version 0.1 (2012/04/26)
 */

require_once dirname(__FILE__) . '/json_client.php';

/**
 * Client for REST communication with the META service, using json as the data format.
 *
 * @see https://wiki.rambla.be/META_REST_API
 */
class MetaService
{
  var $username;
  var $password;
  var $server;
  var $ssl;
  var $json_client;
	
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
    $this->username = $username;
    $this->password = $password;
    $this->server = "meta.meta01.rambla.be";
    if ($server) {
      $this->server = $server;
    }
    $this->ssl = $ssl;
    $this->json_client = new RawsJsonClient($username, $password, $this->server, $ssl, $user_agent);
	}
	
  # Content Methods
  # -------------
  
  /**
   * Get a list of content objects.
   *
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a content feed.
   * @see https://wiki.rambla.be/META_content_resource#Search_content
   */
	function getContentList($querystr = null)
	{
    $uri = "/content/" . $this->username;
    return $this->json_client->GET($uri, $querystr);
	}

  /**
   * Get a single content instance.
   *
   * @param string $name Name that uniquely identifies the content entry.
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a content entry.
   * @see https://wiki.rambla.be/META_content_resource#Get_content_entry
   */
	function getContentInstance($name, $querystr = null)
	{
    $uri = "/content/" . $this->username . "/" . $name . "/";
    return $this->json_client->GET($uri, $querystr);
	}
	
  /**
   * Create a new content instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   * Throws a RawsClientException if the entry argument is invalid.
   *
   * @param mixed $entry Associative array or stdClass object that can be json encoded into a valid content entry.
   * @return stdClass Object corresponding to the content entry that has been created.
   * @see https://wiki.rambla.be/META_content_resource#Create_new_content_instance
   */
	function createContent($entry)
	{
	  $uri = "/content/" . $this->username . "/";
    return $this->json_client->POST($uri, $entry);
	}

  /**
   * Update an existing content instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   * Throws a RawsClientException if the entry argument is invalid.
   *
   * @param mixed $entry Associative array or stdClass object that can be json encoded into a valid content entry.
   * @return stdClass Object corresponding to the content entry that has been updated.
   * @see https://wiki.rambla.be/META_content_resource#Update_existing_content_instance
   */
	function updateContent($entry)
	{
	  $name = "";
	  if (is_array($entry)) {
	    $name = $entry["entry"]["content"]["params"]["name"];
	  }
	  elseif (is_object($entry)) {
	    $name = $entry->entry->content->params->name;
	  }
	  if (! $name) {
      throw new Exception("Invalid content entry passed to updateContent(): the params object should contain a name property!");
	  }
	  $uri = "/content/" . $this->username . "/" . $name . "/";
    return $this->json_client->POST($uri, $entry);
	}

  /**
   * Delete a content instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $name Name that uniquely identifies the content entry.
   * @param bool $delete_files_from_cdn Set to False if you only want to delete the metadata and not the file(s) on the CDN.
   * @see https://wiki.rambla.be/META_content_resource#Delete_content_instance
   */
	function deleteContent($name, $delete_files_from_cdn = True)
	{
	  $uri = "/content/" . $this->username . "/" . $name . "/";
	  $querystr = null;
	  if ($delete_files_from_cdn) {
	    $querystr = "sync_cdn=1";
	  }
    return $this->json_client->DELETE($uri, $querystr);
	}
	
	# GET Contentdir
  # -------------

  /**
   * Get all content (real or temporary) entries for a given CDN directory.
   *
   * @param string $dirpath Path to the CDN directory
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a contentdir feed.
   * @see https://wiki.rambla.be/META_contentdir_resource
   */
	function getContentDirList($dirpath, $querystr = null)
	{
	  $path = "/";
    if ($dirpath) {
      $path = "/" . ltrim($dirpath, "/");
    }
    $uri = "/contentdir/" . $this->username . $path;
    return $this->json_client->GET($uri, $querystr);
	}
  
  # Vocab Methods
  # -------------

  /**
   * Create a new vocab instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param string $name Vocabulary name (= required).
   * @param string $xml_namespace URL of the (XML) namespace (e.g. http://purl.org/dc/elements/1.1/) (= required)
   * @return stdClass Object corresponding to the vocab instance that has been created.
   * @see https://wiki.rambla.be/META_vocab_resource#POST
   */
	function createVocab($name, $xml_namespace)
	{
	  $v = array();
    $v["entry"] = array();
    $v["entry"]["content"] = array();
    $v["entry"]["content"]["params"] = array();
    $v["entry"]["content"]["params"]["name"] = $name;
    $v["entry"]["content"]["params"]["xml_namespace"] = $xml_namespace;
    
	  $uri = "/vocab/" . $this->username . "/";
    return $this->json_client->POST($uri, $v);
	}

  /**
   * Get a list of vocab objects.
   *
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a vocab feed.
   * @see https://wiki.rambla.be/META_vocab_resource#GET
   */
	function getVocabList($querystr = null)
	{
    $uri = "/vocab/" . $this->username;
    return $this->json_client->GET($uri, $querystr);
	}

  /**
   * Get a single vocab instance.
   *
   * @param string $name Name that uniquely identifies the vocab instance.
   * @return stdClass Object corresponding to a vocab entry.
   * @see https://wiki.rambla.be/META_vocab_resource#GET
   */
	function getVocabInstance($name)
	{
    $uri = "/vocab/" . $this->username . "/" . $name . "/";
    return $this->json_client->GET($uri);
	}
	
  /**
   * Update an existing vocab instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $vocab Object corresponding to an existing vocab instance
   * @param string $name Name of the vocab instance to be updated (optional, otherwise the id from the $vocab object is used)
   * @return stdClass Object corresponding to the vocab instance that has been updated.
   * @see https://wiki.rambla.be/META_vocab_resource#POST
   */
	function updateVocab($vocab, $name = null)
	{
    $uri = null;
	  if ($name) {
  	  $uri = "/vocab/" . $this->username . "/" . $name . "/";
	  }
	  else {
	    $uri = $vocab->entry->id;
	  }
    return $this->json_client->POST($uri, $vocab);
	}

  /**
   * Delete a vocab instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $name Name that uniquely identifies the vocab instance.
   * @see https://wiki.rambla.be/META_vocab_resource#DELETE
   */
	function deleteVocab($name)
	{
	  $uri = "/vocab/" . $this->username . "/" . $name . "/";
    return $this->json_client->DELETE($uri);
	}
  
  /**
   * Checks if a vocab with a given name exists.
   *
   * @param string $name Name that uniquely identifies the vocab instance.
   * @return bool True if exists.
   */
  function vocabExists($name) 
  {
    $exists = False;
    $uri = "/vocab/" . $this->username . "/" . $name . "/";
    try {
      $this->json_client->GET($uri);
      $exists = True;
    }
    catch(RawsRequestException $e) {
      $exists = False;
    }
    return $exists;
  }
  
  # GET Ext
  # -------------


  /**
   * Get a list of content objects (via the ext resource).
   *
   * @param string $querystr Query-string to be added to request
   * @return stdClass Object corresponding to a content feed.
   * @see https://wiki.rambla.be/META_ext_resource#json
   */
	function getExtJson($querystr = null)
	{
    $uri = "/ext/json/" . $this->username . "/";
    return $this->json_client->GET($uri, $querystr);
	}

  /**
   * Get an ATOM feed of content objects.
   *
   * @param string $querystr Query-string to be added to request
   * @return string ATOM feed in which each content object corresponds to an entry.
   * @see https://wiki.rambla.be/META_ext_resource#atom
   */
	function getExtAtom($querystr = null)
	{
    $uri = "/ext/atom/" . $this->username . "/";
    return $this->json_client->GET($uri, $querystr, False);
	}

  /**
   * Get a Media RSS feed of content objects.
   *
   * @param string $querystr Query-string to be added to request
   * @return string Media RSS feed in which each content object corresponds to an entry.
   * @param bool If true, an RTMP playlists is being returned (default = False).
   * @see https://wiki.rambla.be/META_ext_resource#mrss
   */
	function getExtMrss($querystr = null, $rtmp = False)
	{
    $uri = "/ext/mrss/" . $this->username . "/";
    if ($rtmp) {
      $uri = "/ext/mrss-jw-rtmp/" . $this->username . "/";
    }
    return $this->json_client->GET($uri, $querystr, False);
	}
  

}
