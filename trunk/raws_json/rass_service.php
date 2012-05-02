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
 * @file Client for communication with the RASS web service.
 *
 * @see https://wiki.rambla.be/RASS_REST_API
 * @package	raws-php
 * @copyright rambla.eu, 2012
 * @version 0.1 (2012/04/26)
 */

require_once dirname(__FILE__) . '/json_client.php';

/**
 * Client for REST communication with the RASS service, using json as the data format.
 *
 * @see https://wiki.rambla.be/RASS_REST_API
 */
class RassService
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
   * @param string $server Name of the web-service (either 'rass.cdn01.rambla.be' or 'rass.cdn02.rambla.be' depending on your sub-CDN).
   * @param bool $ssl Set to True if you're using SSL (default = False, if you want to use SSL for file uploads make sure you have a 'secure' user account - contact support@rambla.be)
   * @param string $user_agent Name of the user agent (is passed in the 'User-Agent' HTTP header).
   */
	function __construct($username, $password, $server, $ssl = False, $user_agent = "raws-php") 
	{
    $this->username = $username;
    $this->password = $password;
    $this->server = $server;
    $this->ssl = $ssl;
    $this->json_client = new RawsJsonClient($username, $password, $server, $ssl, $user_agent);
	}
	  
	
  # Item Methods
  # -------------
 
  /**
   * Upload a file to the CDN + create a RASS item resource.
   *
   * This method will stream the file (= not load the whole file into memory).
   * If a file with the same filename already exist, a suffix will be appended to the uploaded file. So check the entry in the response.
   *
   * @param string $dirpath Relative path to the directory on the CDN in which the file needs to be stored.
   * @param string $filename Preferred name for the file to be created (if a file with the same name already exists at the given location, a suffix will be appended).
   * @param string $local_path Local path to the file that needs to be uploaded.
   * @param bool $create_dirs Create the (sub)directories on the CDN if they don't exist.
   * @return stdClass Corresponds to RASS item entry
   * @see https://wiki.rambla.be/RASS_item_resource#POST_request
   */
 	function createItem($dirpath, $filename, $local_path, $create_dirs)
	{
	  $uri = "/item/";
    $dirpath = ltrim($dirpath, "/");
	  if ($dirpath) {
  	  $uri = $uri . rtrim($dirpath, "/") . "/";
	  }
	  $uri = $uri . $filename . "/";
	  
	  $querystring = null;
	  if ($create_dirs) {
	    $querystring = "post=1";
	  }
	  
	  $extra_headers = array('Slug: ' . $filename);

    return $this->json_client->PUT($uri, $local_path, $querystring, $extra_headers);
	}
	
  /**
   * Download a file from RASS.
   *
   * This method will stream the file to a local path.
   *
   * @param string $path Relative path to the file on the CDN.
   * @param string $local_path Local path to the file that should hold the downloaded file.
   * @return string Path to which the file has been downloaded.
   * @see https://wiki.rambla.be/RASS_item_resource#GET_requests
   */
	function getItem($path, $local_path)
	{
    $uri = "item/" . ltrim($path, "/");
    return $this->json_client->GET_FILE($uri, $local_path);
	}
	
	/**
   * Checks if a RASS item (= file on the CDN) exists?
   *
   * @param string $path Relative path to the file on the CDN.
   * @return bool True if exists
   * @see https://rampubwiki.wiki.rambla.be/RASS_item_resource#HEAD_request
   */
  function itemExists($path)
  {
	  $uri = "/item/" . ltrim($path, "/");
	  return $this->itemUrlExists($uri);
  }
  
	/**
   * Checks if a RASS item (= file on the CDN) exists?
   *
   * @param string $url URL (path) for the item resource.
   * @return bool True if exists
   * @see https://rampubwiki.wiki.rambla.be/RASS_item_resource#HEAD_request
   */
  function itemUrlExists($uri)
  {
    $exists = False;
    try {
      $this->json_client->HEAD($uri);
      $exists = True;
    }
    catch(RawsRequestException $e) {
      $exists = False;
    }
    return $exists;
  }  
 
  /**
   * Delete a file using RASS.
   *
   * This method doesn't return a value. When the DELETE request didn't succeed, an exception is raised.
   *
   * @param string $path Relative path to the file on the CDN.
   * @see https://wiki.rambla.be/RASS_item_resource#DELETE_request
   */
  function deleteItem($path)
  {
	  $uri = "/item/" . ltrim($path, "/");
    return $this->json_client->DELETE($uri);
  }
  
  
  # Dir Methods
  # -------------
 
  /**
   * Create a new directory on the CDN via RASS.
   *
   * @param string $path The location of the directory that needs to be created
   * @param bool $force_create If set to True, POST dir will be used instead of PUT dir => the caller should check the response entry for the actual directory name
   * @return stdClass Corresponds to RASS dir entry
   * @see https://wiki.rambla.be/RASS_dir_resource#POST_request
   */
  public function createDir($path, $force_create = False)
  {
    $uri = "dir/" . ltrim($path, "/");
    if ($force_create) {
      return $this->json_client->POST($uri, null);
    }
    else {
      return $this->json_client->PUT($uri, null);
    }
	}
	
   /**
    * Checks if a RASS dir exists at the given path.
    *
    * @param string $path Relative path to the directory of which the existence needs to be checked
    * @return bool True if exists
    */
   public function dirExists($path)
   {
     $exists = False;
     try {
       $list = $this->json_client->getDirList($path, "kind=root");
       $exists = True;
     }
     catch(RawsRequestException $e) {
       $exists = False;
     }
     return $exists;
   }
	
  /**
   * Get a list of directories from RASS.
   *
   * @param string $path Relative path to a directory on the CDN.
   * @param string $querystr Querystring to be used when calling GET dir.
   * @return stdClass Corresponds to RASS dir feed
   * @see https://wiki.rambla.be/RASS_dir_resource#GET_request
   */
	function getDirList($path, $querystr = null)
	{
    $uri = "dir/" . ltrim($path, "/");
    return $this->json_client->GET($uri, $querystr);
	}
 
  /**
   * Delete a directory using RASS.
   *
   * This method doesn't return a value. When the DELETE request didn't succeed, an exception is raised.
   *
   * @param string $path Relative path to the file on the CDN.
   * @param bool $recursive Delete dir recursively (if False, the DELETE request will fail if files and/or sub-directories exist inside of it).
   * @see https://wiki.rambla.be/RASS_dir_resource#DELETE_request
   */
  function deleteDir($path, $recursive = False)
  {
	  $uri = "dir/" . ltrim($path, "/");
	  $querystr = null;
	  if ($recursive) {
  	  $querystr = "recursive=1";
	  }
    return $this->json_client->DELETE($uri, $querystr);
  }

  # Meta Methods
  # -------------

  /**
   * Sends a GET meta request with the given path.
   *
   * If the path points to a directory on the CDN, a list of meta entries will be returned.
   * If the path points to a file on the CDN, the corresponding meta entry will be returned.
   *
   * @param string Relative path to a file or directory (pass "/" for root-directory) on the CDN
   * @param string $querystr Querystring to be used when calling GET meta.
   * @return stdClass Corresponds to RASS meta list or entry
   * @see https://wiki.rambla.be/RASS_meta_resource
   */
	function getMetaInfo($path, $querystr = null)
	{
    $uri = "meta/" .  $this->username . "/" . ltrim($path, "/");
    return $this->json_client->GET($uri, $querystr);
	}

  /**
   * Sends a GET meta request to retrieve a meta entry, corresponding to a file on the CDN.
   *
   * The path must point to a file on the CDN, otherwise this function will throw a RawsClientException.
   *
   * @param string Relative path to a file on the CDN
   * @param string $querystr Querystring to be used when calling GET meta.
   * @return stdClass Corresponds to RASS meta entry
   * @see https://wiki.rambla.be/RASS_meta_resource
   */
	function getFileMetaInfo($path, $querystr = null)
	{
    $uri = "meta/" .  $this->username . "/" . ltrim($path, "/");
    $entry = $this->json_client->GET($uri, $querystr);
    if (! property_exists($entry, "entry")) {
      throw new RawsClientException("The getFileMetaInfo() response is not an entry: the path argument (= $path) must point to a file !");
    }
	}
 
  /**
   * Sends a GET meta request to retrieve a meta list, corresponding to a directory on the CDN.
   *
   * The path must point to a directory on the CDN, otherwise this function will throw a RawsClientException.
   *
   * @param string Relative path to a directory (pass "/" for root-directory) on the CDN
   * @param string $querystr Querystring to be used when calling GET meta.
   * @return stdClass Corresponds to RASS meta list
   * @see https://wiki.rambla.be/RASS_meta_resource
   */
	function getDirMetaInfo($path, $querystr = null)
	{
    $uri = "meta/" .  $this->username . "/" . ltrim($path, "/");
    $list = $this->json_client->GET($uri, $querystr);
    if (! property_exists($list, "feed")) {
      throw new RawsClientException("The getDirMetaInfo() response is not a list: the path argument (= $path) must point to a directory !");
    }
	}
  

}
