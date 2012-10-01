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
 * @file Client for communication with the RATS web service.
 *
 * @see https://wiki.rambla.be/RATS_REST_API
 * @package	raws-php
 * @copyright rambla.eu, 2012
 * @version 0.1 (2012/04/26)
 */
require_once dirname(__FILE__) . '/json_client.php';

# default output profiles
define('RATS_EXPORT_TO_CDN', "1");

# default input profiles
define('RATS_IMPORT_FROM_CDN', "1");

# default format profiles
define('RATS_FORMAT_MP4_KEEP_SIZE', "1"); # encodes into web quality mp4, retaining width/height of the src
define('RATS_FORMAT_MP4_240P', "2");
define('RATS_FORMAT_MP4_480P', "3");
define('RATS_FORMAT_MP4_720P_WIDE', "4");
define('RATS_FORMAT_MP4_1080P_WIDE', "5");
define('RATS_FORMAT_WEBM_KEEP_SIZE', "11");
define('RATS_FORMAT_WEBM_240P', "12");
define('RATS_FORMAT_WEBM_480P', "13");
define('RATS_FORMAT_WEBM_720P_WIDE', "14");
define('RATS_FORMAT_WEBM_1080P_WIDE', "15");
define('RATS_FORMAT_OGG_KEEP_SIZE', "21");
define('RATS_FORMAT_JPG_KEEP_SIZE', "252");
define('RATS_FORMAT_JPG_480P', "33");
define('RATS_FORMAT_PNG_KEEP_SIZE', "253");

# default formatgroups
define('RATS_FORMATGROUP_MP4_JPG_480P', "1");
define('RATS_FORMATGROUP_HTML5_KEEP_SIZE', "40"); # HTML5 = mp4 + webm + jpg
define('RATS_FORMATGROUP_HTML5_480P', "42");
define('RATS_FORMATGROUP_ADAPTIVE_480P', "27");
define('RATS_FORMATGROUP_ADAPTIVE_KEEP_SIZE', "28");

# default procs
define('RATS_PROC_EMAIL_TXT', "24");
define('RATS_PROC_EMAIL_JSON', "13");
define('RATS_PROC_POST_JSON', "14");

# RATS STATUS CODES
define("RATS_REQUEST_RECEIVED", 1);
define("RATS_IMPORT_IN_PROGRESS", 2);
define("RATS_IMPORT_SUCCEEDED", 3);
define("RATS_TRANS_IN_PROGRESS", 4);
define("RATS_TRANS_SUCCEEDED", 5);
define("RATS_EXPORT_IN_PROGRESS", 6);
define("RATS_FINISHED", 7);
define("RATS_IMPORT_FAILED", 8);
define("RATS_TRANS_FAILED", 9);
define("RATS_EXPORT_FAILED", 10);


/**
 * Client for REST communication with the RATS service, using json as the data format.
 *
 * @see https://wiki.rambla.be/RATS_REST_API
 */
class RatsService
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
    $this->server = "rats.enc01.rambla.be";
    if ($server) {
      $this->server = $server;
    }
    $this->ssl = $ssl;
    $this->json_client = new RawsJsonClient($username, $password, $this->server, $ssl, $user_agent);
	}

  # SRC resource
  # ------------
  
  /**
   * Upload a src to RATS -> create src instance.
   *
   * This method will stream the file (= not load the whole file into memory).
   * If a file with the same filename already exist, a suffix will be appended to the uploaded file. So make sure to check the src id or filename in the response entry.
   *
   * @param string $filename Preferred name for the file to be created (if a file with the same name already exists at the given location, a suffix will be appended).
   * @param string $local_path Local path to the file that needs to be uploaded.
   * @return stdClass Corresponds to RATS src entry
   * @see https://wiki.rambla.be/RATS_src_resource#POST_src
   */
 	function createSrc($filename, $local_path)
	{
	  $uri = "/src/";
	  $extra_headers = array('Slug: ' . $filename);

    return $this->json_client->PUT($uri, $local_path, null, $extra_headers);
	}
	
	/**
   * Get a single src instance.
   *
   * @param string $filename Name that uniquely identifies the src instance.
   * @return stdClass Object corresponding to a src entry.
   * @see https://wiki.rambla.be/RATS_src_resource#GET
   */
	function getSrcInstance($filename)
	{
    $uri = "/src/" . $this->username . "/" . $filename . "/";
    return $this->json_client->GET($uri);
	}
  
	 /**
    * Get a list of src instances.
    *
    * @param string $querystr Querystring to be used when calling GET src.
    * @return stdClass Corresponds to RATS src feed
    */
 	function getSrcList($querystr = null)
	{
	  $uri = "/src/";
    return $this->json_client->GET($uri, $querystr);
	}

  /**
   * Delete a src instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $filename Name that uniquely identifies the src instance.
   * @see https://wiki.rambla.be/RATS_src_resource#DELETE
   */
	function deleteSrc($filename)
	{
	  $uri = "/src/" . $this->username . "/" . $filename . "/";
    return $this->json_client->DELETE($uri);
	}
  
	
	# JOB resource
	# ------------

  /**
   * Launch a single transcoding job (= one src file => one transcoded file) and publish the transcoded file on the CDN.
   *
   * This method assumes that the src file was previously uploaded to RATS (see createJob()).
   * This method will publish the transcoded file on the CDN, at the location specified in the $tgt_location parameter.
   *
   * @param string $format              ID of the transcoding format profile (e.g. RATS_FORMAT_MP4_KEEP_SIZE).
   * @param string $src_filename        Name of the RATS src (typically created as the result of calling $this->createSrc())
   * @param string $tgt_location        Location (= relative path + filename without extension) at which the transcoded file will be published on the CDN.
   * @param string $client_passthru     Placeholder for data to receive back in the job's report (NOTE: all XML reserved characters should be escaped).
   * @param string $proc               Comma-separated string containing Proc IDs, to be executed while processing the job.
   * @param string $client_input        Additional data to be used by the Procs (see https://wiki.rambla.be/RATS_job_resource#proc_.26_client_input)
   * @param string $startpos            Offset in seconds (or percentage, using the '%'-sign) from the beginning of the src file, at which transcoding must start.
   * @param string $endpos              Offset in seconds from the end of the src file, at which transcoding must end.
   * @return stdClass Corresponds to RATS job entry
   * @see https://wiki.rambla.be/RATS_job_resource
   */
  public function createSingleJob($format, $src_filename, $tgt_location, $client_passthru = null, $proc = null, $client_input = null, $startpos = null, $endpos = null)
  {
    return $this->createJob($format, null, null, $src_filename, "1", $tgt_location, null, $client_passthru, $proc, $client_input, $startpos, $endpos);
  }

  /**
   * Launch a batch transcoding (= one src file => multiple transcoded files) and publish the transcoded files on the CDN.
   *
   * This method assumes that the src file was previously uploaded to RATS (see createJob()).
   * This method will publish the transcoded files on the CDN, at the location specified in the $tgt_location parameter.
   *
   * @param string $formatgroup         ID of the formatgroup (= group of format profiles that will be used for the jobs).
   * @param string $src_filename        Name of the RATS src (typically created as the result of calling $this->createSrc())
   * @param string $tgt_location        Location (= relative path + filename without extension) at which the transcoded files will be published on the CDN.
   * @param string $snapshot_interval   Integer that indicates the interval at which snapshots should be taken (= if a snapshot profile is part of the formatgroup).
   * @param string $client_passthru     Placeholder for data to receive back in the job's report (NOTE: all XML reserved characters should be escaped).
   * @param string $proc               Comma-separated string containing Proc IDs, to be executed while processing the job.
   * @param string $client_input        Additional data to be used by the Procs (see https://wiki.rambla.be/RATS_job_resource#proc_.26_client_input)
   * @return stdClass Corresponds to RATS job entry
   * @see https://wiki.rambla.be/RATS_job_resource
   */
  public function createBatchJob($formatgroup, $src_filename, $tgt_location, $snapshot_interval = null, $client_passthru = null, $proc = null, $client_input = null)
  {
    return $this->createJob(null, $formatgroup, null, $src_filename, "1", $tgt_location, $snapshot_interval, $client_passthru, $proc, $client_input);
  }

  /**
   * Launch a new transcoding job, by creating a new RATS job instance (POST /job). 
   *
   * This method lets you choose freely which job params to set.
   * Some params may not be combined, so only use this method if you are familiar with the RATS API.
   * Otherwise, use one of the other createXxxJob() methods instead.
   *
   * @param string $format                  ID of the transcoding format profile.
   * @param string $formatgroup             ID of the formatgroup (= group of format profiles that will be used for the jobs).
   * @param string $input                   ID of the input profile, or null (= if the src has been uploaded to RATS).
   * @param string $src_or_input_location   Either the filename of an existing src (if $input == null) or a given import location (if $input != null).
   * @param string $output                  ID of the output profile, or null (if the transcoded file doesn't need to be exported).
   * @param string $tgt_location            Path or filename to be used when exporting the transcoded file, or null (= use the src filename).
   * @param string $snapshot_interval       Integer that indicates the interval at which snapshots should be taken (= if a snapshot profile is part of the formatgroup).
   * @param string $client_passthru         Placeholder for data to receive back in the job's report (NOTE: all XML reserved characters should be escaped).
   * @param string $proc                   Comma-separated string containing Proc IDs, to be executed while processing the job.
   * @param string $client_input            Additional data to be used by the Procs (see https://wiki.rambla.be/RATS_job_resource#proc_.26_client_input)
   * @param string $startpos                Offset in seconds (or percentage, using the '%'-sign) from the beginning of the src file, at which transcoding must start.
   * @param string $endpos                  Offset in seconds from the end of the src file, at which transcoding must end.
   * @return stdClass Corresponds to RATS job entry
   * @see https://wiki.rambla.be/RATS_job_resource
   */
  public function createJob($format = null, $formatgroup = null, $input = null, $src_or_input_location = null, $output = null, $tgt_location = null,
                            $snapshot_interval = null, $client_passthru = null, $proc = null, $client_input = null, $startpos = null, $endpos = null)
  {
    $e = $this->json_client->get_empty_entry_array();
    
    # set format or formatgroup
    if ($format && $formatgroup) {
      throw new RawsClientException("Invalid parameters passed to createJob(): both format and formatgroup were set. These can not be combined!");
    }
    if ($format) { $e["entry"]["content"]["params"]["format"] = $format; }
    elseif ($formatgroup) { $e["entry"]["content"]["params"]["formatgroup"] = $formatgroup; }
    else {
      throw new RawsClientException("Invalid parameters passed to createJob(): can't create a job without a format or formatgroup ID.");
    }
    
    # set input+import_location or src_location
    if (! $src_or_input_location) {
      throw new RawsClientException("Invalid parameters passed to createJob(): can't create a job without a src_location or import_location!");
    }
    if ($input) {
      $e["entry"]["content"]["params"]["input"] = $input;
      $e["entry"]["content"]["params"]["import_location"] = $src_or_input_location;
    } else {
      $e["entry"]["content"]["params"]["src_location"] = $src_or_input_location;
    }

    # set other params
    if ($output) { $e["entry"]["content"]["params"]["output"] = $output; }
    if ($tgt_location) { $e["entry"]["content"]["params"]["tgt_location"] = $tgt_location; }
    if ($snapshot_interval) { $e["entry"]["content"]["params"]["snapshot_interval"] = $snapshot_interval; }
    if ($client_passthru) { $e["entry"]["content"]["params"]["client_passthru"] = $client_passthru; }
    if ($proc) { $e["entry"]["content"]["params"]["proc"] = $proc; }
    if ($client_input) { $e["entry"]["content"]["params"]["client_input"] = $client_input; }
    if ($startpos) { $e["entry"]["content"]["params"]["startpos"] = $startpos; }
    if ($endpos) { $e["entry"]["content"]["params"]["endpos"] = $endpos; }
    
	  $uri = "/job/";
    return $this->json_client->POST($uri, $e);
    
  }
	
  /**
   * Retrieves a job entry. 
   *
   * @param string $uri URL (path) for the job instance
   * @return stdClass Object corresponding to a job entry.
   * @see https://wiki.rambla.be/RATS_job_resource#GET_job
   */
	function getJob($uri)
	{
    return $this->json_client->GET($uri);
	}

  /**
   * Checks if a job has been completed.
   *
   * The method will do a GET job request, and overwrite the job entry passed as argument with the new one.
   * If the job is part of a batch, it will only return True if all jobs from the batch are completed.
   * If the argument does not contain stdClass representation of a job entry, this function will throw a RawsClientException.
   *
   * @param stdClass $job Object corresponding to a job entry.
   * @return bool True if job has been completed.
   * @see https://wiki.rambla.be/RATS_job_resource
   */
	function isJobComplete(&$job)
	{
	  $completed = False;
    if (! property_exists($job, "entry")) {
      throw new RawsClientException("Invalid argument passed to getJob(): you must pass an stdClass object that corresponds to the entry of an existing job.");
    }
    
    # get the current job representation
    $job = $this->getJob($job->entry->id);
    
    # check if job is complete
    $batch_status = (int)$job->entry->content->params->batch_status;
    if ($batch_status == -1) {
      $job_status = (int)$job->entry->content->params->status;
      if ($job_status >= RATS_FINISHED) { $completed = True; }
    }
    elseif ($batch_status == 100) {
      $completed = True;
    }
    return $completed;
	}

  /**
   * Get a single job instance.
   *
   * @param string $id ID that uniquely identifies the job instance.
   * @return stdClass Object corresponding to a job entry.
   * @see https://wiki.rambla.be/RATS_job_resource
   */
	function getJobInstance($id)
	{
	  $uri = "/job/" . $id;
    return $this->json_client->GET($uri);
	}

	 /**
    * Get a list of job instances.
    *
    * @param string $querystr Querystring to be used when calling GET job.
    * @return stdClass Corresponds to RATS job feed
    */
 	function getJobList($querystr = null)
	{
	  $uri = "/job/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	# INPUT resource
	# -------------------------------

  /**
    * Get a list of input instances.
    *
    * @param string $querystr Querystring to be used when calling GET input.
    * @return stdClass Corresponds to RATS input feed
    */
 	function getInputList($querystr = null)
	{
	  $uri = "/input/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	/**
   * Get a single input instance.
   *
   * @param string $id ID that uniquely identifies the input instance.
   * @return stdClass Object corresponding to a input entry.
   * @see https://wiki.rambla.be/RATS_input_resource
   */
	function getInputInstance($id)
	{
	  $uri = "/input/" . $id;
    return $this->json_client->GET($uri);
	}
  
	/**
   * Create a new input instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param array $a_params Associative array of input params.
   * @return stdClass Object corresponding to a input entry.
   * @see https://wiki.rambla.be/RATS_input_resource
   */
	function createInput($a_params)
	{
	  $e = array();
    $e["entry"] = array();
    $e["entry"]["content"] = array();
    $e["entry"]["content"]["params"] = $a_params;
    return $this->json_client->POST("/input/", $e);
	}
	
	/**
   * Update an existing input instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $input Corresponds to a RATS input entry
   * @return stdClass Corresponds to RATS input entry
   * @see https://wiki.rambla.be/RATS_input_resource
   */
 	function updateInput($input)
	{
    return $this->json_client->POST($input->entry->id, $input);
	}
	
	/**
   * Delete input instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id ID that uniquely identifies the input instance.
   * @see https://wiki.rambla.be/RATS_input_resource#DELETE
   */
	function deleteInput($id)
	{
	  $uri = "/input/" . $id;
    return $this->json_client->DELETE($uri);
	}
  
	# OUTPUT resource
	# -------------------------------

  /**
    * Get a list of output instances.
    *
    * @param string $querystr Querystring to be used when calling GET output.
    * @return stdClass Corresponds to RATS output feed
    */
 	function getOutputList($querystr = null)
	{
	  $uri = "/output/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	/**
   * Get a single output instance.
   *
   * @param string $id ID that uniquely identifies the output instance.
   * @return stdClass Object corresponding to a output entry.
   * @see https://wiki.rambla.be/RATS_output_resource
   */
	function getOutputInstance($id)
	{
	  $uri = "/output/" . $id;
    return $this->json_client->GET($uri);
	}
  
	/**
   * Create a new output instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param array $a_params Associative array of output params.
   * @return stdClass Object corresponding to a output entry.
   * @see https://wiki.rambla.be/RATS_output_resource
   */
	function createOutput($a_params)
	{
	  $e = array();
    $e["entry"] = array();
    $e["entry"]["content"] = array();
    $e["entry"]["content"]["params"] = $a_params;
    return $this->json_client->POST("/output/", $e);
	}
	
	/**
   * Update an existing output instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $output Corresponds to a RATS output entry
   * @return stdClass Corresponds to RATS output entry
   * @see https://wiki.rambla.be/RATS_output_resource
   */
 	function updateOutput($output)
	{
    return $this->json_client->POST($output->entry->id, $output);
	}
	
	/**
   * Delete output instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id ID that uniquely identifies the output instance.
   * @see https://wiki.rambla.be/RATS_output_resource#DELETE
   */
	function deleteOutput($id)
	{
	  $uri = "/output/" . $id;
    return $this->json_client->DELETE($uri);
	}
	
	# FORMAT resource
	# -------------------------------

  /**
    * Get a list of format instances.
    *
    * @param string $querystr Querystring to be used when calling GET format.
    * @return stdClass Corresponds to RATS format feed
    */
 	function getFormatList($querystr = null)
	{
	  $uri = "/format/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	/**
   * Get a single format instance.
   *
   * @param string $id ID that uniquely identifies the format instance.
   * @return stdClass Object corresponding to a format entry.
   * @see https://wiki.rambla.be/RATS_format_resource
   */
	function getFormatInstance($id)
	{
	  $uri = "/format/" . $id;
    return $this->json_client->GET($uri);
	}
  
	/**
   * Create a new format instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param array $a_params Associative array of format params.
   * @return stdClass Object corresponding to a format entry.
   * @see https://wiki.rambla.be/RATS_format_resource
   */
	function createFormat($a_params)
	{
	  $e = array();
    $e["entry"] = array();
    $e["entry"]["content"] = array();
    $e["entry"]["content"]["params"] = $a_params;
    return $this->json_client->POST("/format/", $e);
	}
	
	/**
   * Update an existing format instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $format Corresponds to a RATS format entry
   * @return stdClass Corresponds to RATS format entry
   * @see https://wiki.rambla.be/RATS_format_resource
   */
 	function updateFormat($format)
	{
    return $this->json_client->POST($format->entry->id, $format);
	}
	
	/**
   * Delete format instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id ID that uniquely identifies the format instance.
   * @see https://wiki.rambla.be/RATS_format_resource#DELETE
   */
	function deleteFormat($id)
	{
	  $uri = "/format/" . $id;
    return $this->json_client->DELETE($uri);
	}
  
	# TRANSC resource
	# ------------

	 /**
    * Get a list of transc instances.
    *
    * @param string $querystr Querystring to be used when calling GET transc.
    * @return stdClass Corresponds to RATS transc feed
    */
 	function getTranscList($querystr = null)
	{
	  $uri = "/transc/";
    return $this->json_client->GET($uri, $querystr);
	}
	
  /**
   * Get a single transc instance.
   *
   * @param string $filename Name that uniquely identifies the transc instance.
   * @return stdClass Object corresponding to a transc entry.
   * @see https://wiki.rambla.be/RATS_transc_resource
   */
	function getTranscInstance($filename)
	{
	  $uri = "/transc/" . $this->username . "/" . $filename;
    return $this->json_client->GET($uri);
	}
	
	/**
   * Download the transc file from RATS.
   *
   * This method will stream the file to a local path.
   *
   * @param string $filename Name that uniquely identifies the transc instance.
   * @param string $local_path Local path to the file that should hold the downloaded file.
   * @return string Path to which the file has been downloaded.
   * @see https://wiki.rambla.be/RATS_transc_resource
   */
	function getTranscFile($filename, $local_path)
	{
	  $uri = "/transc/" . $this->username . "/" . $filename;
    return $this->json_client->GET_FILE($uri, $local_path);
	}
  
  /**
   * Delete a transc instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $filename Name that uniquely identifies the transc instance.
   * @see https://wiki.rambla.be/RATS_transc_resource#DELETE
   */
	function deleteTransc($filename)
	{
	  $uri = "/transc/" . $this->username . "/" . $filename;
    return $this->json_client->DELETE($uri);
	}
  
	# OVERLAY resource
	# ----------------
	
  /**
   * Upload a overlay file to RATS -> create overlay instance.
   *
   * If a file with the same filename already exist, a suffix will be appended to the uploaded file. So make sure to check the src id or filename in the response entry.
   *
   * @param string $filename Preferred name for the file to be created (if a file with the same name already exists at the given location, a suffix will be appended).
   * @param string $local_path Local path to the file that needs to be uploaded.
   * @return stdClass Corresponds to RATS overlay entry
   * @see https://wiki.rambla.be/RATS_src_resource#POST_src
   */
 	function createOverlay($filename, $local_path)
	{
	  $uri = "/overlay/";
	  $extra_headers = array('Slug: ' . $filename);

    return $this->json_client->PUT($uri, $local_path, null, $extra_headers);
	}

	
  /**
   * Update an existing overlay instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $overlay Corresponds to a RATS overlay entry
   * @return stdClass Corresponds to RATS overlay entry
   * @see https://wiki.rambla.be/RATS_overlay_resource
   */
 	function updateOverlay($overlay)
	{
    return $this->json_client->POST($overlay->entry->id, $overlay);
	}
	
  /**
   * Get a single overlay instance.
   *
   * @param string $id ID that uniquely identifies the overlay instance.
   * @return stdClass Object corresponding to a overlay entry.
   * @see https://wiki.rambla.be/RATS_overlay_resource
   */
	function getOverlayInstance($id)
	{
	  $uri = "/overlay/" . $id;
    return $this->json_client->GET($uri);
	}

   /**
    * Get a list of overlay instances.
    *
    * @param string $querystr Querystring to be used when calling GET overlay.
    * @return stdClass Corresponds to RATS overlay feed
    */
 	function getOverlayList($querystr = null)
	{
	  $uri = "/overlay/";
    return $this->json_client->GET($uri, $querystr);
	}

  /**
   * Delete a overlay instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id ID that uniquely identifies the overlay instance.
   * @see https://wiki.rambla.be/RATS_overlay_resource#DELETE
   */
	function deleteOverlay($id)
	{
	  $uri = "/overlay/" . $id;
    return $this->json_client->DELETE($uri);
	}
 	
 	## FORMATGROUP
 	## -----------
 	
 	/**
    * Get a list of formatgroup instances.
    *
    * @param string $querystr Querystring to be used when calling GET formatgroup.
    * @return stdClass Corresponds to RATS formatgroup feed
    */
 	function getFormatgroupList($querystr = null)
	{
	  $uri = "/formatgroup/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	/**
   * Get a single formatgroup instance.
   *
   * @param string $id ID that uniquely identifies the formatgroup instance.
   * @return stdClass Object corresponding to a formatgroup entry.
   * @see https://wiki.rambla.be/RATS_formatgroup_resource
   */
	function getFormatgroupInstance($id)
	{
	  $uri = "/formatgroup/" . $id;
    return $this->json_client->GET($uri);
	}
  
	/**
   * Create a new formatgroup instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param array $a_params Associative array of formatgroup params.
   * @return stdClass Object corresponding to a formatgroup entry.
   * @see https://wiki.rambla.be/RATS_formatgroup_resource
   */
	function createFormatgroup($a_params)
	{
	  $e = array();
    $e["entry"] = array();
    $e["entry"]["content"] = array();
    $e["entry"]["content"]["params"] = $a_params;
    return $this->json_client->POST("/formatgroup/", $e);
	}
	
	/**
   * Update an existing formatgroup instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $formatgroup Corresponds to a RATS formatgroup entry
   * @return stdClass Corresponds to RATS formatgroup entry
   * @see https://wiki.rambla.be/RATS_formatgroup_resource
   */
 	function updateFormatgroup($formatgroup)
	{
    return $this->json_client->POST($formatgroup->entry->id, $formatgroup);
	}
	
	/**
   * Delete formatgroup instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id ID that uniquely identifies the formatgroup instance.
   * @see https://wiki.rambla.be/RATS_formatgroup_resource#DELETE
   */
	function deleteFormatgroup($id)
	{
	  $uri = "/formatgroup/" . $id;
    return $this->json_client->DELETE($uri);
	}
  
 	## PROC
 	## -----------
 	
 	/**
    * Get a list of proc instances.
    *
    * @param string $querystr Querystring to be used when calling GET proc.
    * @return stdClass Corresponds to RATS proc feed
    */
 	function getProcList($querystr = null)
	{
	  $uri = "/proc/";
    return $this->json_client->GET($uri, $querystr);
	}
	
	/**
   * Get a single proc instance.
   *
   * @param string $id ID that uniquely identifies the proc instance.
   * @return stdClass Object corresponding to a proc entry.
   * @see https://wiki.rambla.be/RATS_proc_resource
   */
	function getProcInstance($id)
	{
	  $uri = "/proc/" . $id;
    return $this->json_client->GET($uri);
	}
  
	/**
   * Create a new proc instance.
   *
   * Throws a RawsRequestException if the instance could not be created.
   *
   * @param array $a_params Associative array of proc params.
   * @return stdClass Object corresponding to a proc entry.
   * @see https://wiki.rambla.be/RATS_proc_resource
   */
	function createProc($a_params)
	{
	  $e = array();
    $e["entry"] = array();
    $e["entry"]["content"] = array();
    $e["entry"]["content"]["params"] = $a_params;
    return $this->json_client->POST("/proc/", $e);
	}
	
	/**
   * Update an existing proc instance.
   *
   * Throws a RawsRequestException if the instance could not be updated.
   *
   * @param stdClass $proc Corresponds to a RATS proc entry
   * @return stdClass Corresponds to RATS proc entry
   * @see https://wiki.rambla.be/RATS_proc_resource
   */
 	function updateProc($proc)
	{
    return $this->json_client->POST($proc->entry->id, $proc);
	}
	
	/**
   * Delete proc instance.
   *
   * Throws a RawsRequestException if the instance could not be deleted.
   *
   * @param string $id ID that uniquely identifies the proc instance.
   * @see https://wiki.rambla.be/RATS_proc_resource#DELETE
   */
	function deleteProc($id)
	{
	  $uri = "/proc/" . $id;
    return $this->json_client->DELETE($uri);
	}
  
 	
 	
	# SRCENCODE resource
	# ----------------
	
	/**
   * Upload a src to RATS, to be automatically encoded and published.
   *
   * This method will stream the file (= not load the whole file into memory).
   *
   * @see https://wiki.rambla.be/RATS_srcencode_resource#PUT
   * @param string $filename Preferred name for the file to be created (if a file with the same name already exists at the given location, a suffix will be appended).
   * @param string $local_path Local path to the file that needs to be uploaded.
   * @param string $formatgroup_id Numerical ID of your custom RATS formatgroup, to be used for transcoding the src file.
   * @param string $secret Shared secret attached to your custom RATS formatgroup.
   * @param int $hmac_valid_seconds Number of seconds during which the HMAC will be considered as valid by RATS (default = 30).
   * @param string $publish_dir Directory on the CDN in which transcoded files will be published (default = root).
   * @param string $client_passthru Client specific data in JSON format (should be valid json, use json_encode() to generate it !!),
   *                                which can be retrieved later as part of a notification or as the result of polling the job resource (optional).
   * @param array $proc_ids Array consisting of strings that refer to a proc ID (e.g. array(RATS_PROC_EMAIL_JSON, RATS_PROC_POST_JSON)).
   * @return JSON object containing the URL of a RATS job that has been launched
   * @see https://wiki.rambla.be/RATS_src_resource#POST_src
   */
 	function doSrcencode($filename, $local_path, $formatgroup_id, $formatgroup_secret, $hmac_valid_seconds = 30, $publish_dir = "", $client_passthru = "", $proc_ids = array())
	{
	  $uri = "/srcencode_m/" . $this->username . "/";
	  $msg_data = uniqid(rand(), true); # generate unique id
    date_default_timezone_set('Europe/Brussels');
    $msg_timestamp = time() + $hmac_valid_seconds; # requests using this page will be valid during three hours
    $raws_hmac = md5($formatgroup_secret.$msg_timestamp.$msg_data);
    
    $proc_ids = json_encode($proc_ids);
    $raws_info = <<<EOT
{"msg_data":"$msg_data","msg_timestamp":"$msg_timestamp","publish_filename":"$filename","publish_dir":"$publish_dir","formatgroup_id":"$formatgroup_id","client_passthru":$client_passthru,"proc_ids":$proc_ids}
EOT;
 	  $extra_headers = array('x-raws-info: ' . $raws_info, "x-raws-hmac: " . $raws_hmac);

    return $this->json_client->PUT($uri, $local_path, null, $extra_headers);
	}
 

}
