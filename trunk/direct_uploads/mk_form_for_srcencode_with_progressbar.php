<?php
# Sample to demonstrate how to generate a basic form that allows end users to upload a file to RAWS using a progressbar.
# The progressbar uses the 'upload_progress' resource to retrieve progress data from RAWS via ajax requests.
# For more info about retrieving progress data, see https://wiki.rambla.be/RAWS_upload_progress
#
# This sample uploads files directly to the RATS server, for automatic encoding (including snapshots) and publishing on the CDN.
# This is done using the RATS 'srcencode' resource, which uses HMAC authentication.
# For more info about the srcencode resource, see https://wiki.rambla.be/RATS_srcencode_resource
#
# This sample depends on jQuery and the 'jquery.uploadProgress.js' script (available in the 'js' sub-directory) for display of the progressbar.
# Note that the 'upload_progress' resource can be used with any progressbar script, as long as it conforms to the API.
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
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  	<head>
  		<title>srcencode demo with progressbar</title>
  	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    		<script src="./js/jquery.uploadProgress.js"></script>
  	  	<script type="text/javascript">
  			$(function() {
  				$('form').uploadProgress({
  					/* scripts locations for webkit */
  					jqueryPath: "http://code.jquery.com/jquery-1.7.2.min.js",
            progressUrl: "http://rats.enc01.rambla.be/upload_progress",
  					uploadProgressPath: "./js/jquery.uploadProgress.js",
  					start:function(){},
  					uploading: function(upload) {\$('#percents').html(upload.percents+'%');},
  					interval: 1000
  			    });
  			});
  		</script>
  		<style type="text/css">
  			.bar {
  			  width: 300px;
  			}

  			#progress {
  			  background: #eee;
  			  border: 1px solid #222;
  			  margin-top: 20px;
  			}
  			#progressbar {
  			  width: 0px;
  			  height: 24px;
  			  background: #333;
  			}
  		</style>
  	</head>

  	<body>
    <h1>demo for automatic encoding and publication, with a progressbar for the file upload</h1>
    <p>This sample demonstrates how to upload files directly to RATS and get them automatically encoded (including snapshots) and published on the CDN. This is done using the RATS <a href="https://wiki.rambla.be/RATS_srcencode_resource">srcencode</a> resource and HMAC authentication. Additionally, a progressbar is being displayed that uses the <a href=https://wiki.rambla.be/RAWS_upload_progress_resource">upload_progress</a> resource to retrieve progress data from RAWS.</p>
  <p>This sample depends on jQuery and the 'jquery.uploadProgress.js' script (in the 'js' sub-directory) for getting the process data and rendering the progressbar. Note that the 'upload_progress' resource can be used with any progressbar script, as long as the script ajax calls conform to the API.</p>

      <form id="upload" action="http://rats.enc01.rambla.be/srcencode/$username/" enctype="multipart/form-data" method="POST">
      <fieldset>
      <legend>Select files to upload:</legend>
        <input type="file" id="file1" name="file1" /><br />
        <input type="file" id="file2" name="file2" /><br />
        <input type="file" id="file3" name="file3" /><br />
        <input type="submit" name = "submit" value="Upload File(s)" id="submit /">
        <input type="hidden" name="raws_info" value="{&quot;formatgroup_id&quot;:&quot;$formatgroup_id&quot;,&quot;redirect&quot;:&quot;$redirect&quot;,&quot;msg_data&quot;:&quot;$msg_data&quot;,&quot;msg_timestamp&quot;:&quot;$msg_timestamp&quot;,&quot;publish_dir&quot;:&quot;$publish_dir&quot;,&quot;snapshot_interval&quot;:&quot;$snapshot_interval&quot;,&quot;proc_ids&quot;:[&quot;$proc_id&quot;],&quot;client_passthru&quot;:&quot;$client_passthru&quot;}" />
        <input type="hidden" name="raws_hmac" value="$hmac">
      </fieldset>
      </form>
  	    <div id="uploading">
  	      <div id="progress" class="bar">
  	        <div id="progressbar">&nbsp;</div>
  	      </div>
  	    </div>
  		<div id="percents"></div>
  	</body>
  </html>
EOT;

$file = './form_for_srcencode_with_progressbar.html';
file_put_contents($file, $html);


?>