<?php
# Sample to demonstrate how to generate HTML which can be used to upload files directly to the CDN ('file_upload1' request).
# For more info, see https://wiki.rambla.be/RASS_file_upload1_resource
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

# To run this sample, make sure the variables below are set correctly (see see https://wiki.rambla.be/RASS_file_upload1_resource)

# required variables
$username = "xxx";                              # name of your Rambla user account
$cdn = "cdn0x";                                 # sub-CDN on which you user account is located (e.g. "cdn01")
$secret = "xxx";                                # the secret from the RASS user settings, should not be made public !!
$redirect = "http://example.com/";              # URL to redirect to after upload is completed# required variables

$msg_data = uniqid(rand(), true);               # generate unique msg_data value (for HMAC)
date_default_timezone_set('Europe/Brussels');
$msg_timestamp = time() + (3 * 60 * 60);        # requests using this page will be valid during three hours
$hmac = md5($secret.$msg_timestamp.$msg_data);  # generate the HMAC

# optional variables
$publish_dir = "mydir";

$html = <<<EOT
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  <meta charset="utf-8">
  <title>Test file_upload1</title>
  </head>
  <body>
    <form id="MyForm" action="http://rass.$cdn.rambla.be/file_upload/$username/" enctype="multipart/form-data" method="POST">
    <input type="file" name="MyFile" />
    <input type="submit" value="Upload">
    <input type="hidden" name="raws_info" value="{&quot;redirect&quot;:&quot;$redirect&quot;,&quot;msg_data&quot;:&quot;$msg_data&quot;,&quot;msg_timestamp&quot;:&quot;$msg_timestamp&quot;,&quot;publish_dir&quot;:&quot;$publish_dir&quot;}" />
    <input type="hidden" name="raws_hmac" value="$hmac">
    </form>
  </body>
EOT;

$file = './test_file_upload.html';
file_put_contents($file, $html);


?>