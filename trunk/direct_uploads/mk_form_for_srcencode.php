<?php
# Sample to demonstrate how to generate a basic form that allows end users to upload a file directly to the RATS server,
#  which will automatically be encoded (including snapshots) and published on the CDN.
# This is done using the RATS 'srcencode' resource, which uses HMAC authentication.
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
$publish_dir = "mydir/mysubdir/";                # publish encoded files in this directory on the CDN
$snapshot_interval = "25";                      # take snapshot after every 25% of the video
$proc_id = "13";                                # email CDN report in JSON format
$client_passthru = "some client specific data"; # client-specific data that can be retrieved later (via GET job or CDN report notification)

$html = <<<EOT
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
	<title>basic srcencode demo</title>
  </head>
  <body>
  <h1>demo for automatic encoding and publication</h1>
  <p>This sample demonstrates how to upload files directly to RATS and get them automatically encoded (including snapshots) and published on the CDN. This is done using the RATS <a href="https://wiki.rambla.be/RATS_srcencode_resource">srcencode</a> resource and HMAC authentication.</p>

    <form id="MyForm" action="http://rats.enc01.rambla.be/srcencode/$username/" enctype="multipart/form-data" method="POST">
    <fieldset>
    <input type="file" name="file1" /><br />
    <input type="file" name="file2" /><br />
    <input type="submit" value="Upload">
    <input type="hidden" name="raws_info" value="{&quot;formatgroup_id&quot;:&quot;$formatgroup_id&quot;,&quot;redirect&quot;:&quot;$redirect&quot;,&quot;msg_data&quot;:&quot;$msg_data&quot;,&quot;msg_timestamp&quot;:&quot;$msg_timestamp&quot;,&quot;publish_dir&quot;:&quot;$publish_dir&quot;,&quot;snapshot_interval&quot;:&quot;$snapshot_interval&quot;,&quot;proc_ids&quot;:[&quot;$proc_id&quot;],&quot;client_passthru&quot;:&quot;$client_passthru&quot;}" />
    <input type="hidden" name="raws_hmac" value="$hmac">
    </fieldset>
    </form>
  </body>
  </html>
EOT;

$file = './form_for_srcencode.html';
file_put_contents($file, $html);


?>