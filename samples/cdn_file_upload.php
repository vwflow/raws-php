<?php
# Sample to demonstrate how to upload a file to the CDN via the RASS file_upload resource (authenticating with a HMAC).
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
require_once 'raws_json/rass_service.php';

# To run this sample, define these variables first
define('USER', "xxx"); # name of your Rambla user account
define('PWD', "xxx"); # password of your Rambla user account
define('RASS_SERVER', 'rass.cdnXX.rambla.be'); # either 'rass.cdn01.rambla.be' or 'rass.cdn02.rambla.be' (depending on the subCDN on which your account is located)

$local_path = "/path/to/local/file";  # path to the file that will be published
$filename = "myfilename.mp4"; # name to be given to the file on the CDN
$publish_dir = "mydir";               # directory on the CDN in which to publish the file (leave empty to publish in root-directory)
$secret = "xxx";                      # the secret from the RASS user settings, should not be made public !!
$seconds_valid = 30;                  # the number of seconds this request will remain valid

try {
  $rats = new RassService(USER, PWD, RASS_SERVER);
  
  # RASS PUT file_upload
  echo "\nUploading file to CDN, please wait..";
  $res = $rats->doFileUpload($filename, $local_path, $secret, $seconds_valid, $publish_dir);
  foreach($resp->href as $href) {
    echo "\nFile published on CDN: " . $href;
  }

}
catch(Exception $e) {
  echo "\nAn exception occurred with code = " . $e->getCode();
  echo "\nError msg = " . $e->getMessage();
}

?>