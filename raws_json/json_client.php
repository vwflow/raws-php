<?php

class ClientException extends Exception 
{
  public function __construct($message, $code=0) {
     parent::__construct($message,$code);
  }   
}

class RequestException extends Exception 
{
  public function __construct($message, $code=500) {
     parent::__construct($message,$code);
  }   
}
  

class JsonClient
{
  var $username;
  var $password;
  var $server;
  var $ssl;
	
	function __construct($username, $password, $server, $ssl = False) 
	{
    $this->username = $username;
    $this->password = $password;
    $this->server = $server;
    $this->ssl = $ssl;
	}

  /**
   * Create the full URL for making a RAWS request.
   *
   * @param string $uri URI path or full URI.
   * @param string $querystr Query-string to be appended to the URI.
   * @return string Full URI for the RAWS request
   */
	function get_url($path, $querystr = null)
	{
	  $url = "";
	  if (0 != substr_compare($path, "http", 0, 4))
	  {
  	  $url = "http://";
  	  if ($this->ssl) {
    	  $url = "https://";
  	  }
  	  $url = $url . $this->server . "/" . ltrim($path, "/");
	  }
	  else 
	  {
	    $url = $path;
	  }
	  if ($querystr) {
	    $url = $url . "?" . ltrim($querystr, "?");
	  }
#	  echo "\nURL:" . $url;
	  return $url;
	}
	
  /**
   * Do a GET request to retrieve a json feed or entry.
   *
   * @param string $uri URI path or full URI.
   * @param string $querystr Query-string to be appended to the URI.
   * @param decode $decode Set this to False if you don't want the response body to be decoded from json into a stdObject.
   * @return stdObject corresponding to a json entry or feed, or null (in case of DELETE)
   */
	function GET($uri, $querystr = null, $decode = True)
	{
	  $url = $this->get_url($uri, $querystr);
	  return $this->do_request($url, "GET", null, null, $decode);
	}
	
  /**
   * Do a GET request to download a file from RASS.
   *
   * @param string $uri URI path or full URI.
   * @param string $filepath Local path to the file that needs to be downloaded.
   * @return string Local path to the file that has been downloaded.
   */
	function GET_FILE($uri, $filepath)
	{
	  # add alt=json in querystr to get error responses in json
	  #  curl doesn't send Accept header if CURLOPT_RETURNTRANSFER is False
	  #  => so RAWS will use default responder in case of error
	  $url = $this->get_url($uri, "alt=json"); 
	  return $this->do_get_file_request($url, $filepath);
	}

  /**
   * Do a POST request with json-encoded data in the body.
   *
   * @param string $uri URI path or full URI.
   * @param array or stdObject $data If the request body contains data, this should contain a json serializable array or stdObject.
   * @param string $querystr Query-string to be appended to the URI.
   * @return stdObject corresponding to a json entry
   */
	function POST($uri, $data, $querystr = null)
	{
	  $url = $this->get_url($uri, $querystr);
	  return $this->do_request($url, "POST", $data);
	}

  // function POST_file($uri, $filepath, $extra_headers)
  // {
  //   $url = $this->get_url($uri);
  //   return $this->do_request($url, "POST", null, $filepath, True, $extra_headers);
  // }

  /**
   * Do a PUT request to upload a file.
   *
   * This method will stream the file (= not load the whole file into memory).
   *
   * @param string $uri URI path or full URI.
   * @param string $filepath Local path to the file that needs to be uploaded.
   * @param string $querystr Query-string to be appended to the URI.
   * @param array $extra_headers Sequential array of headers (strings) to be added to the request headers.
   * @return entry Json entry
   */
	function PUT($uri, $filepath, $querystr = null, $extra_headers = null)
	{
	  $url = $this->get_url($uri, $querystr);
	  return $this->do_request($url, "PUT", null, $filepath, True, $extra_headers);
	}

  /**
   * Do a DELETE request.
   *
   * A succeeded delete request doesn't return data.
   * If the request doesn't succeed, an exception is thrown.
   *
   * @param string $uri URI path or full URI.
   * @param string $querystr Query-string to be appended to the URI.
   */
	function DELETE($uri, $querystr = null)
	{
	  $url = $this->get_url($uri, $querystr);
	  return $this->do_request($url, "DELETE");
	}

  /**
   * Do a HTTP request to RAWS using json as the data format.
   *
   * This method will encode the request body to json (unless $filepath != null) + decode the response body from json to an stdObject (unless $decode == False).
   *
   * If an HTTP error status is returned by RAWS, this method raises an exception.
   *  The exception will contain the HTTP $code and $msg returned by RAWS.
   *
   * If the method call succeeds, a PHP stdObject is returned (which is the result of json decoding the response)
   *  Depending on the call, this may be a feed (= list) or entry object.
   *  In case of DELETE calls which don't generate a response, the method returns null (if no exception is raised, the DELETE call has succeeded).
   *
   * @param string $url Full RAWS URL, including the querystring
   * @param string $method Name of the HTTP method in capital letters ('GET', 'POST', 'PUT', 'DELETE', 'HEAD' are supported).
   * @param array or stdObject $data If the request body contains data, this should contain a json serializable array or stdObject.
   * @param string $filepath If the request body is a file (binary), this should contain the path to the file to be uploaded.
   * @param decode $decode Set this to False if you don't want the response body to be decoded from json into a stdObject.
   * @param array $extra_headers Sequential array of headers (strings) to be added to the request headers.
   * @return stdObject corresponding to a json entry or feed, or null (in case of DELETE)
   */
  function do_request($url, $method, $data = null, $filepath = null, $decode = True, $extra_headers = null) 
  {
   $curl_handle = curl_init();
   
   # set HTTP headers
   $headers = array();
   # unless $decode is False, we want the response to be encoded in to json
   if ($decode !== False) {
     $headers[] = 'Accept: application/json';
   }
	 # if we're uploading a file, set the SLUG header + Content-Length of the payload
   if ($filepath) {
     $headers[] = 'Content-Type: video/*';
   }
   else {
     $headers[] = 'Content-Type: application/json';
     $data = json_encode($data);
   }
	 if ($extra_headers) {
     if (!empty($extra_headers)) {
       $headers = array_merge($headers, $extra_headers);
     }
   }

   curl_setopt($curl_handle, CURLOPT_URL, $url);
   curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
   //curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
   //curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);

   curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ":" . $this->password);

   switch ($method) {
     case 'GET':
       if ($filepath) {
         curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, False);
         $fh = fopen($filepath,'wb+');
         curl_setopt($curl_handle,CURLOPT_FILE,$fh);
       }
       break;
     case 'POST':
#       echo "\nPOSTING DATA:";
#       print_r($data);
       if ($filepath) { # POST can also be used to upload a file (however it can not do streaming uploads)
         $data = file_get_contents($filepath);
         $headers[] = 'Content-Length: ' . filesize($filepath);
        }
       curl_setopt($curl_handle, CURLOPT_POST, true);
       curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
       break;
     case 'PUT':
#       echo "\n PUT Request to url: " . $url;
       if ($filepath) { # upload file
         curl_setopt($curl_handle,CURLOPT_PUT,true);
         curl_setopt($curl_handle,CURLOPT_INFILE,fopen($filepath,'rb')); // load the file in by its resource handle
         curl_setopt($curl_handle, CURLOPT_INFILESIZE, filesize($filepath));
       }
       else {
         curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
         curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $data);
       }
       break;
     case 'DELETE':
       curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
       break;
   }

   $response = curl_exec($curl_handle);
   $code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
   curl_close($curl_handle);
#   echo "\n Response code: " . $code;
#   echo "\n Response: " . $response;

   if ($code >= 300) {
     throw new RequestException($response, $code);
   }

   if ($response) {
     if ($decode !== False) {
       $response = json_decode($response);
     }
   }

   return $response;
  }
  
  /**
   * Do a HTTP request to RAWS to download a file.
   *
   * If the file download fails, an exception is raised.
   *
   * @param string $url Full RAWS URL, including the querystring
   * @param string $filepath Local path to the file that needs to be downloaded.
   * @return string Local path to the file that has been downloaded.
   */
  function do_get_file_request($url, $filepath)
  {
    # check if tgt_location_local is writable
    $res = fopen($filepath, 'wb+');
    if (! $res) {
      throw new ClientException("Unable to open file location '" . $filepath . "' for writing.");
    }
    fclose($res);

    $headers = array();
    $curl_handle = curl_init();

    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
    //curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
    //curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ":" . $this->password);

    # instructs curl not to return response in string, but stream to file instead
    #  Note: this also happens in case of error response (see WORKAROUND below)
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, False);
    $fh = fopen($filepath,'wb+');
    curl_setopt($curl_handle,CURLOPT_FILE,$fh);

    $response = curl_exec($curl_handle);
    $code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

    curl_close($curl_handle);
    if ($fh != Null) fclose($fh);

    # WORKAROUND for handling errors : error response has been written to file (with path == $filepath)
    #  => read error response from the file and delete it
    if ($code >= 300) {
      // read the xml from the file and store it in the body var
      $frc = fopen($filepath, 'r+');
      $response = fread($frc, filesize($filepath));
      fclose($frc);
      // delete file
      unlink($filepath);
      throw new RequestException($response, $code);
    }

    return $filepath;
  }
  
}