<?php

require_once dirname(__FILE__) . '/json_client.php';

class MetaService
{
  var $username;
  var $password;
  var $server;
  var $ssl;
  var $json_client;
	
	function __construct($username, $password, $server = null, $ssl = False) 
	{
    $this->username = $username;
    $this->password = $password;
    $this->server = "meta.meta01.rambla.be";
    if ($server) {
      $this->server = $server;
    }
    $this->ssl = $ssl;
    $this->json_client = new JsonClient($username, $password, $server, $ssl);
	}
	
  # Content Methods
  # -------------
  
	function getContentList($querystr = null)
	{
    $uri = "/content/" . $this->username;
    return $this->json_client->GET($uri, $querystr);
	}

	function getContentInstance($name, $querystr = null)
	{
    $uri = "/content/" . $this->username . "/" . $name . "/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	function createContent($entry)
	{
	  $uri = "/content/" . $this->username . "/";
    return $this->json_client->POST($uri, $entry);
	}

	function updateContent($name, $entry)
	{
	  $uri = "/content/" . $this->username . "/" . $name . "/";
    return $this->json_client->POST($uri, $entry);
	}

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

	function getVocabList($querystr = null)
	{
    $uri = "/vocab/" . $this->username;
    return $this->json_client->GET($uri, $querystr);
	}

	function getVocabInstance($name)
	{
    $uri = "/vocab/" . $this->username . "/" . $name . "/";
    return $this->json_client->GET($uri);
	}
	
	function updateVocab($vocab, $name = null)
	{
    $uri = null;
	  if ($name) {
  	  $uri = "/vocab/" . $this->username . "/" . $name . "/";
	  }
	  else {
	    $uri = $vocab->entry->id;
#	    echo "\n updateVocab uri = " . $uri;
	  }
    return $this->json_client->POST($uri, $vocab);
	}

	function deleteVocab($name)
	{
	  $uri = "/vocab/" . $this->username . "/" . $name . "/";
    return $this->json_client->DELETE($uri);
	}
  
  # GET Ext
  # -------------

	function getExtJson($querystr = null)
	{
    $uri = "/ext/json/" . $this->username . "/";
    return $this->json_client->GET($uri, $querystr);
	}

	function getExtAtom($querystr = null)
	{
    $uri = "/ext/atom/" . $this->username . "/";
    return $this->json_client->GET($uri, $querystr, False);
	}

	function getExtMrss($querystr = null)
	{
    $uri = "/ext/mrss/" . $this->username . "/";
    return $this->json_client->GET($uri, $querystr, False);
	}
  

}
