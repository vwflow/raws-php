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
    $this->json_client = new RawsJsonClient($username, $password, $server, $ssl, $user_agent);
	}

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
   * @param string $client_input        Additional data to be used by the Procs (see https://rampubwiki.wiki.rambla.be/RATS_job_resource#proc_.26_client_input)
   * @param string $startpos            Offset in seconds (or percentage, using the '%'-sign) from the beginning of the src file, at which transcoding must start.
   * @param string $endpos              Offset in seconds from the end of the src file, at which transcoding must end.
   * @return stdClass Corresponds to RATS job entry
   * @see https://rampubwiki.wiki.rambla.be/RATS_job_resource
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
   * @param string $client_input        Additional data to be used by the Procs (see https://rampubwiki.wiki.rambla.be/RATS_job_resource#proc_.26_client_input)
   * @return stdClass Corresponds to RATS job entry
   * @see https://rampubwiki.wiki.rambla.be/RATS_job_resource
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
   * @param string $client_input            Additional data to be used by the Procs (see https://rampubwiki.wiki.rambla.be/RATS_job_resource#proc_.26_client_input)
   * @param string $startpos                Offset in seconds (or percentage, using the '%'-sign) from the beginning of the src file, at which transcoding must start.
   * @param string $endpos                  Offset in seconds from the end of the src file, at which transcoding must end.
   * @return stdClass Corresponds to RATS job entry
   * @see https://rampubwiki.wiki.rambla.be/RATS_job_resource
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
	
	

}
