# google-youtube-api-privacystatus
Script for updating Youtube video's privacy status using OAuth

This code is to be run automatically to update a Youtube video's privacy status

First, generate your key using "get-token.php" - read the notes below for generation
Next, update this file with the appropriate information (path to key file, Client ID, 
   Client Secret (OAuth Required), Application Name, Database Login, Database Query, and
   location of PHP Client Library - all download information is below)

@author Kyle Perkins
@site https://github.com/kode29/google-youtube-api-privacystatus

NOTICE: Rest of copyright should be in tact for other scripts (Dom Sammut (domsammut.com) and Ibrahim Ulukaya (Google)
Last Update: 20160108

Primary code from https://www.domsammut.com/code/php-server-side-youtube-v3-oauth-api-video-upload-guide/
Mixed with sample code from https://developers.google.com/youtube/v3/docs/videos/update (PHP #1)

#Before you begin

##Generate OAuth credentials
Create Client ID and Client Secret by creating OAuth credentialsat https://console.developers.google.com/apis/credentials
MAKE SURE YOU UPDATE YOUR REDIRECT URL TO MATCH!!!!!!!!!

##Save the return in a file
Generate the "the_key" with get-token.php and store it into "the_key.txt" or wherever you want to store it BEFORE running this script.
Also, make sure "the_key" has a REFRESH TOKEN!

##Get the Google PHP Client Library
Download the PHP Client Library from Google at https://developers.google.com/api-client-library/php/

##Set the permissions
Make sure that your server can read/write "the_key.txt" or else you'll get a "Cannot json decode" error

##Update the variables
Variables to set up are $CLIENT_ID, $CLIENT_SECRET, $application_name, $VIDEO_ID (if not already pulled), path to $key_file, PDO SQL Login, path to PHP Client Library, and $redirect (redirect url)
