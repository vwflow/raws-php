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
 * @file Client for communication with the RASE web service.
 *
 * @see https://wiki.rambla.be/RASE_REST_API
 * @package	raws-php
 * @copyright rambla.eu, 2012
 * @version 0.1 (2012/04/26)
 */
require_once dirname(__FILE__) . '/json_service.php';

/**
 * Client for REST communication with the RASE service, using json as the data format.
 *
 * @see https://wiki.rambla.be/RASE_REST_API
 */
class RaseService extends JsonService
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
    $this->server = "rase.str01.rambla.be";
    if ($server) {
      $this->server = $server;
    }
    parent::__construct($username, $password, $this->server, $ssl, $user_agent);
	}


	 /**
    * Get a list of wowza applications.
    *
    * @param string $querystr Querystring to be used when calling GET wowapp.
    * @return stdClass Corresponds to RASE wowapp feed
    */
 	function getWowappList($querystr = null)
	{
	  $uri = "/wowapp/";
    return $this->json_client->GET($uri, $querystr);
	}


}
