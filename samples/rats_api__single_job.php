<?php
# Sample to demonstrate usage of the RATS job resource.
#
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

# To run this sample, define these variables first
define('USER', "xxx"); # name of your Rambla user account
define('PWD', "xxx"); # password of your Rambla user account
define('LOCAL_PATH', '/path/to/local/video/file'); # add path to local (video) file, to be uploaded to the CDN

require_once 'raws_json/json_client.php';
require_once 'raws_json/rats_service.php';

try {
  $rats = new RatsService(USER, PWD);

  # upload file to RATS
  echo "\nUploading file to RATS, please wait..";
  $src = $rats->createSrc("test_rats_api_src", LOCAL_PATH);
  echo "\nCreated src: " . $src->entry->id . "\n";
  echo "\nSrc has filename = " . $src->entry->content->params->filename . "\n";

  # launch single encoding job
  $job = $rats->createSingleJob(RATS_FORMAT_MP4_KEEP_SIZE,              # format profile to be used
                                $src->entry->content->params->filename, # name of the RATS src we've just uploaded
                                "test/rats_single_job/mymovie",         # location on the CDN for publishing (mymovie = filename, RATS adds the right extension)
                                "some_client_specific_info",            # some client specific info (useful in an asynchronous scenario)
                                RATS_PROC_EMAIL_TXT . "," . RATS_PROC_EMAIL_JSON); # comma-separated list of procs => instruct RATS to send emails when encoding is done
  echo "\nCreated single job with id: " . $job->entry->id;

  # wait until job has completed
  while (! $rats->isJobComplete($job)) {
    echo "\n.. job not yet complete, sleeping 10 seconds before checking again";
    sleep(10); # sleep number of seconds before asking again
  }

  echo "\nJob has completed with status = " . $job->entry->content->params->status;

  # in an asynchronous scenario, you can retrieve the client_passthru from the cdn_report
  if ($job->entry->cdn_report->client_passthru) {
    echo "\n\nThe client_passthru field contains the following string: " . $job->entry->cdn_report->client_passthru; 
  }
  
  # if metadata is enabled for your user account => a META content instance has been created (see https://rampubwiki.wiki.rambla.be/META_content_resource)
  if ($job->entry->cdn_report->content) {
    echo "\n\nA META content instance has been created at " . $job->entry->cdn_report->content; 
  }

  # if YouTube syncing is enabled for your user account => the ID of the YouTube video can be retrieved from the CDN report
  if ($job->entry->cdn_report->yt_id) {
    echo "\nThe video has been exported to YouTube, ID = " . $job->entry->cdn_report->yt_id; 
  }

  # the cdn_report contains URLs for download + different kinds of streaming
  echo "\n\nInfo about the encoded file(s):";
  foreach ($job->entry->cdn_report->public_uri as $info) {
    echo "\n- URL for (progressive) download: " . $info->href;
    echo "\n  * width = " . $info->width;
    echo "\n  * height = " . $info->height;
    if ($info->video_bitrate) { echo "\n  * video_bitrate = " . $info->video_bitrate; }
    if ($info->audio_bitrate) { echo "\n  * audio_bitrate = " . $info->audio_bitrate; }
  }
  echo "\n\nRTMP streaming:";
  foreach ($job->entry->cdn_report->rtmp_uri as $info) {
    echo "\n- RTMP streamer URI: " . $info->streamer . ", RTMP location: " . $info->location;
  }
  echo "\n\nApple Http Streaming (HLS):";
  foreach ($job->entry->cdn_report->apple_http_uri as $info) {
    echo "\n- HLS URI: " . $info->href;
  }

}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>