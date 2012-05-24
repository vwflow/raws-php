<?php
# Sample to demonstrate how to publish files on the CDN, after having them encoded into different formats (authenticating with a HMAC).
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

require_once 'raws_json/json_client.php';
require_once 'raws_json/rats_service.php';

# To run this sample, define these variables first
define('USER', "xxx"); # name of your Rambla user account
define('PWD', "xxx"); # password of your Rambla user account

$local_path = "/path/to/local/file";  # path to the file that will be published
$filename = "myfilename.mp4";         # name to be given to the file on the CDN
$publish_dir = "mydir";               # directory on the CDN in which to publish the file (leave empty to publish in root-directory)
$formatgroup_id = "xxx";              # formatgroup id to be used
$secret = "xxx";                      # the secret from the RASS user settings, should not be made public !!
$seconds_valid = 30;                  # the number of seconds this request will remain valid
$client_passthru = "my metadata";     # client-specific data that can be retrieved later (via GET job or CDN report notification)
$proc_ids = array()                   # array containing strings that each refer to the ID of a processing action (e.g. rats.RATS_PROC_EMAIL_TXT)

try {
  $rats = new RatsService(USER, PWD);

  # RATS PUT srcencode
  echo "\nUploading file to RATS, please wait..";
  $resp = $rats->doSrcencode($filename, $local_path, $formatgroup_id, $secret, $seconds_valid, $publish_dir, json_encode($client_passthru), $proc_ids);
  foreach($resp->job as $job) {
    echo "\nJob URL: " . $job;
  }

}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>