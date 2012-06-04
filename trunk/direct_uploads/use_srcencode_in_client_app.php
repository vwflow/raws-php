<?php
# Sample to demonstrate how to upload a file directly to the RATS server, which will automatically be encoded (including snapshots) and published on the CDN.
# This is done using the RATS 'srcencode_m' resource, which uses HMAC authentication.
# For more info, see https://wiki.rambla.be/RATS_srcencode_m_resource
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
  # no pwd needed, since rats->doSrcencode() sends a HMAC based on the $secret
  $rats = new RatsService(USER, null);

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