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
 * @file Common base class for web-service clients.
 *
 * @package	raws-php
 * @copyright rambla.eu, 2012
 * @version 4.0 (2012/10/10)
 */
require_once dirname(__FILE__) . '/json_client.php';

class JsonService
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
   * @param string $server Name of the web-service
   * @param bool $ssl Set to True if you're using SSL (default = False, if you want to use SSL for file uploads make sure you have a 'secure' user account - contact support@rambla.be)
   * @param string $user_agent Name of the user agent (is passed in the 'User-Agent' HTTP header).
   */
  function __construct($username, $password, $server, $ssl, $user_agent = "raws-php") 
  {
      $this->username = $username;
      $this->password = $password;
      $this->server = $server;
      if ($server) {
        $this->server = $server;
      }
      $this->ssl = $ssl;
      $this->json_client = new RawsJsonClient($username, $password, $this->server, $ssl, $user_agent);
  }
  
  /**
   * Follow the URL stored in the 'next' link element of the feed, and return the feed in the server response (or null).
   *
   * @param stdClass $list Object corresponding to a feed
   * @param stdClass Object corresponding to the next feed request. Returns null if no 'next' feed is available.
   */
  public function getNextFeed($list)
  {
    # can't do anything without a feed
    if (! $list) {
      return null; # TODO : should raise error
    }
    
    # see if there's a next link in the feed -> if so, take its url
    $url = null;
    foreach($list->feed->link as $link) {
      if ($link->rel == "next") {
        $url = $link->href;
      }
    }
    # No next link available -> EOF
    if (! $url) {
      return null;
    }
    
    return $this->json_client->do_request($url, "GET");
  }

  
  /**
   * Return the URL stored in the 'next' link element of the feed, and return the feed in the server response (or null).
   *
   * @param stdClass $list Object corresponding to a feed
   * @return string URL inside the 'next' link element of the feed, or null.
   */
  public function getNextLink($list)
  {
    # can't do anything without a feed
    if (! $list) {
      return null; # TODO : should raise error
    }
    
    # see if there's a next link in the feed -> if so, take its url
    $url = null;
    foreach($list->feed->link as $link) {
      if ($link->rel == "next") {
        $url = $link->href;
        break;
      }
    }
    
    return $url;
  }

}  