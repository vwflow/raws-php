<?php
# Sample to demonstrate how to generate HTML which can be used as a HTTP hotfolder ('srcencode' request).
# For more info, see https://wiki.rambla.be/RATS_srcencode_resource
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

# To run this sample, make sure the variables below are set correctly (see see https://wiki.rambla.be/RATS_srcencode_resource)

# required variables
$username = "xxx";                              # name of your Rambla user account
$formatgroup_id = "xxx";                        # formatgroup id to be used
$secret = "xxx";                                # the secret from the formatgroup, should not be made public !!
$redirect = "http://example.com/";              # URL to redirect to after upload is completed

$msg_data = uniqid(rand(), true);               # generate unique msg_data value (for HMAC)
date_default_timezone_set('Europe/Brussels');
$msg_timestamp = time() + (3 * 60 * 60);        # requests using this page will be valid during three hours
$hmac = md5($secret.$msg_timestamp.$msg_data);  # generate the HMAC

# optional variables
$export_dir = "mydir/mysubdir/";                # publish encoded files in this directory on the CDN
$snapshot_interval = "25";                      # take snapshot after every 25% of the video
$proc_id = "13";                                # email CDN report in JSON format
$client_passthru = "some client specific data"; # client-specific data that can be retrieved later (via GET job or CDN report notification)

$html = <<<EOT
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
  <title>Test srcencode</title>
  </head>
  <body>
    <form id="MyForm" action="http://rats.enc01.rambla.be/srcencode/$username/" enctype="multipart/form-data" method="POST">
    <input type="file" name="rats_file" />
    <input type="submit" value="Upload">
    <input type="hidden" name="rats_info" value="{&quot;formatgroup_id&quot;:&quot;$formatgroup_id&quot;,&quot;redirect&quot;:&quot;$redirect&quot;,&quot;msg_data&quot;:&quot;$msg_data&quot;,&quot;msg_timestamp&quot;:&quot;$msg_timestamp&quot;,&quot;export_dir&quot;:&quot;$export_dir&quot;,&quot;snapshot_interval&quot;:&quot;$snapshot_interval&quot;,&quot;proc_ids&quot;:[&quot;$proc_id&quot;],&quot;client_passthru&quot;:&quot;$client_passthru&quot;}" />
    <input type="hidden" name="rats_hmac" value="$hmac">
    </form>
  </body>
EOT;

$file = './test_srcencode.html';
file_put_contents($file, $html);


?>