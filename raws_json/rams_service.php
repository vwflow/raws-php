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
 * @file Client for communication with the RAMS web service.
 *
 * @see https://wiki.rambla.be/RAMS_REST_API
 * @package	raws-php
 * @copyright rambla.eu, 2012
 * @version 1.0 (2012/10/10)
 */
require_once dirname(__FILE__) . '/json_service.php';

/**
 * Client for REST communication with the RASE service, using json as the data format.
 *
 * @see https://wiki.rambla.be/RASE_REST_API
 */
class RamsService extends JsonService
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
    $this->server = "rams.mon01.rambla.be";
    if ($server) {
      $this->server = $server;
    }
    parent::__construct($username, $password, $this->server, $ssl, $user_agent);
	}


	 /**
    * Get a list of traffic instances.
    *
    * @param string $querystr Querystring to be used when calling GET traffic.
    * @return stdClass Corresponds to RAMS traffic feed
    */
 	function getTrafficList($path = null, $querystr = null)
	{
	  $uri = "/traffic/";
	  if ($path) {
      $uri .= ltrim($path, "/");
    }
    return $this->json_client->GET($uri, $querystr);
	}

	 /**
    * Get a list of total instances.
    *
    * @param string $querystr Querystring to be used when calling GET total.
    * @return stdClass Corresponds to RAMS total feed
    */
 	function getTotalList($querystr = null)
	{
	  $uri = "/total/";
    return $this->json_client->GET($uri, $querystr);
	}

	 /**
    * Get a list of storage instances.
    *
    * @param string $querystr Querystring to be used when calling GET storage.
    * @return stdClass Corresponds to RAMS storage feed
    */
 	function getStorageList($querystr = null)
	{
	  $uri = "/storage/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	 /**
    * Get a list of concurrent (v1) instances.
    *
    * @param string $querystr Querystring to be used when calling GET concurrent.
    * @return stdClass Corresponds to RAMS concurrent feed
    */
 	function getConcurrentV1List($querystr = null)
	{
	  $uri = "/concurrent/v1/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	
  //  /**
  //     * Get a list of concurrent (v2) instances.
  //     *
  //     * @param string $querystr Querystring to be used when calling GET concurrent.
  //     * @return stdClass Corresponds to RAMS concurrent feed
  //     */
  //    function getConcurrentList($app = null, $path = null, $querystr = null)
  // {
  //   $uri = "/concurrent/v2/";
  //   if ($app) {
  //       $uri .= trim($app, "/") . "/";
  //      if ($path) {
  //        $uri .= trim($path, "/") . "/";
  //    }
  //     }
  //     
  //     return $this->json_client->GET($uri, $querystr);
  // }
	
	 /**
    * Get a list of (geo) domain instances.
    *
    * @param string $querystr Querystring to be used when calling GET geo/domain.
    * @return stdClass Corresponds to RAMS domain feed
    */
 	function getDomainList($querystr = null)
	{
	  $uri = "/geo/domain/";
    return $this->json_client->GET($uri, $querystr);
	}

	 /**
    * Get a list of (geo) domain instances.
    *
    * @param string $querystr Querystring to be used when calling GET geo/city.
    * @return stdClass Corresponds to RAMS city feed
    */
 	function getCityList($querystr = null)
	{
	  $uri = "/geo/city/";
    return $this->json_client->GET($uri, $querystr);
	}

	 /**
    * Get a list of (geo) host instances.
    *
    * @param string $querystr Querystring to be used when calling GET geo/host.
    * @return stdClass Corresponds to RAMS host feed
    */
 	function getHostList($querystr = null)
	{
	  $uri = "/geo/host/";
    return $this->json_client->GET($uri, $querystr);
	}

	 /**
    * Get a list of filter instances.
    *
    * @param string $querystr Querystring to be used when calling GET filter.
    * @return stdClass Corresponds to RAMS filter feed
    */
 	function getFilterList($querystr = null)
	{
	  $uri = "/filter/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	 /**
    * Get a list of traffic-type instances.
    *
    * @param string $querystr Querystring to be used when calling GET traffic-type.
    * @return stdClass Corresponds to RAMS traffic-type feed
    */
 	function getTrafficTypeList($querystr = null)
	{
	  $uri = "/traffic-type/";
    return $this->json_client->GET($uri, $querystr);
	}

	 /**
    * Get a list of payed instances.
    *
    * @param string $querystr Querystring to be used when calling GET customer/payed.
    * @return stdClass Corresponds to RAMS payed feed
    */
 	function getPayedList($querystr = null)
	{
	  $uri = "/customer/payed/";
    return $this->json_client->GET($uri, $querystr);
	}

	 /**
    * Get a list of Rambla users.
    *
    * @param string $querystr Querystring to be used when calling GET customer/users.
    * @return stdClass Corresponds to RAMS users feed
    */
 	function getUsersList($querystr = null)
	{
	  $uri = "/customer/users/";
    return $this->json_client->GET($uri, $querystr);
	}

	 /**
    * Get a list of used instances.
    *
    * @param string $querystr Querystring to be used when calling GET customer/used.
    * @return stdClass Corresponds to RAMS used feed
    */
 	function getUsedList($querystr = null)
	{
	  $uri = "/customer/used/";
    return $this->json_client->GET($uri, $querystr);
	}

 	
	

}
