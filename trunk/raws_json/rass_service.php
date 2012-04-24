<?php

require_once dirname(__FILE__) . '/json_client.php';

class RassService
{
  var $username;
  var $password;
  var $server;
  var $ssl;
  var $json_client;
	
	function __construct($username, $password, $server, $ssl = False) 
	{
    $this->username = $username;
    $this->password = $password;
    $this->server = $server;
    $this->ssl = $ssl;
    $this->json_client = new JsonClient($username, $password, $server, $ssl);
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
   * @return stdObject Corresponds to RASS item entry
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
   */
	function getItem($path, $local_path)
	{
    $uri = "item/" . ltrim($path, "/");
    return $this->json_client->GET_FILE($uri, $local_path);
	}
 
  /**
   * Delete a file using RASS.
   *
   * This method doesn't return a value. When the DELETE request didn't succeed, an exception is raised.
   *
   * @param string $path Relative path to the file on the CDN.
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
   * @return stdObject Corresponds to RASS dir entry
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
   * Get a list of directories from RASS.
   *
   * @param string $path Relative path to a directory on the CDN.
   * @param string $querystr Querystring to be used when calling GET dir.
   * @return stdObject Corresponds to RASS dir feed
   */
	function getDirFeed($path, $querystr = null)
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
  

}
