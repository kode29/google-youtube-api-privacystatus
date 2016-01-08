<?php
/**
 * This code is to be run automatically to update a Youtube video's privacy status
 *
 * First, generate your key using "get-token.php" - read the notes below for generation
 * Next, update this file with the appropriate information (path to key file, Client ID, 
 *    Client Secret (OAuth Required), Application Name, Database Login, Database Query, and
 *    location of PHP Client Library - all download information is below)
 * 
 * @author Kyle Perkins
 * @site https://github.com/kode29/google-youtube-api-privacystatus
 * 
 * NOTICE: Rest of copyright should be in tact for other scripts (Dom Sammut (domsammut.com) and Ibrahim Ulukaya (Google)
 * Last Update: 20160108
**/

#Primary code from https://www.domsammut.com/code/php-server-side-youtube-v3-oauth-api-video-upload-guide/
# Mixed with sample code from https://developers.google.com/youtube/v3/docs/videos/update (PHP #1)


#Generate the "the_key" with get-token.php and store it into "the_key.txt" or wherever you want to store it BEFORE running this script.
# Also, make sure "the_key" has a REFRESH TOKEN!
$key_file = "/path/to/the_key.txt";

#Create Client ID and Client Secret by creating OAuth credentials 
# at https://console.developers.google.com/apis/credentials
# MAKE SURE YOU UPDATE YOUR REDIRECT URL TO MATCH!!!!!!!!!
$CLIENT_ID = "XXXXXXXXXXXXXX.apps.googleusercontent.com";
$CLIENT_SECRET = "XXXXXXXXXXX";
$application_name="APPLICATION-NAME";

#CHeck the DB for updated videos
$video_list=array();
    $dbh = new PDO('mysql:host=localhost;dbname=DATABASE_NAME', "DATABASE_USER", "DATABASE_PW");

	$sql="select `video` from `TABLE` where `stamp` like '".date("Y-m-d H:i:")."%'";
				$query = $dbh -> prepare($sql);
				$query->execute();
				if ($query->rowCount() > 0){ #rowCount() won't work on some databases
					$values = $query->fetch(PDO::FETCH_ASSOC);
					while (list($key, $value) = each($values)){
						$video_list[]=$value;
					}
				}
$key = file_get_contents($key_file);
if (count($video_list)>0){
foreach($video_list as $VIDEO_ID){
	$VIDEO_ID = str_replace("https://youtube.com/watch?v=", "", $VIDEO_ID);
	$VIDEO_ID = str_replace("https://youtu.be/", "", $VIDEO_ID);

#Sample $VIDEO_ID can be "gYY3fVz6PjY";
/**
 * This sample adds new tags to a YouTube video by:
 *
 * 1. Retrieving the video resource by calling the "youtube.videos.list" method
 *    and setting the "id" parameter
 * 2. Appending new tags to the video resource's snippet.tags[] list
 * 3. Updating the video resource by calling the youtube.videos.update method.
 *
 * @author Ibrahim Ulukaya
*/

// Call set_include_path() as needed to point to your client library.
#Download the PHP Client Library from Google at https://developers.google.com/api-client-library/php/

#This has been installed using Composer - update if you download the files directly
set_include_path(get_include_path() . PATH_SEPARATOR . '/PATH/TO/vendor/google/apiclient/src/');
    
require_once 'Google/Client.php';
require_once 'Google/Service/YouTube.php';
session_start();

/*
 * You can acquire an OAuth 2.0 client ID and client secret from the
 * Google Developers Console <https://console.developers.google.com/>
 * For more information about using OAuth 2.0 to access Google APIs, please see:
 * <https://developers.google.com/youtube/v3/guides/authentication>
 * Please ensure that you have enabled the YouTube Data API for your project.
 */
$OAUTH2_CLIENT_ID = $CLIENT_ID;
$OAUTH2_CLIENT_SECRET = $CLIENT_SECRET;

$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setScopes('https://www.googleapis.com/auth/youtube');

#$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], FILTER_SANITIZE_URL);
# If running via Cron, HTTP_HOST may be blank
$redirect = filter_var('http://YOUR_URL/' . $_SERVER['PHP_SELF'], FILTER_SANITIZE_URL);
$client->setRedirectUri($redirect);

$scope=array("https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtubepartner", "https://www.googleapis.com/auth/youtube.forcessl");

// Define an object that will be used to make all API requests.


#if (isset($_GET['code'])) {
#  if (strval($_SESSION['state']) !== strval($_GET['state'])) {
#    die('The session state did not match.');
#  }
#
#  $client->authenticate($_GET['code']);
#  $_SESSION['token'] = $client->getAccessToken();
#  header('Location: ' . $redirect);
#}
#
#if (isset($_SESSION['token'])) {
#  $client->setAccessToken($_SESSION['token']);
#}
$client_id = $CLIENT_ID;
$client_secret = $CLIENT_SECRET;
#var_dump($key);

  $client = new Google_Client();
    $client->setApplicationName($application_name);
    $client->setClientId($client_id);
    $client->setAccessType('offline');
    $client->setAccessToken($key);
    $client->setScopes($scope);
    $client->setClientSecret($client_secret);

// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken()) {
/**
         * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
         */
        if($client->isAccessTokenExpired()) {
            $newToken = json_decode($client->getAccessToken());
            $client->refreshToken($newToken->refresh_token);
		#This is for debugging if your token is not regenerated
	    #var_dump($client->getAccessToken());
            file_put_contents($key_file, $client->getAccessToken());
        }

$youtube = new Google_Service_YouTube($client);

  try{

    // REPLACE this value with the video ID of the video being updated.
    $videoId = $VIDEO_ID;

    // Call the API's videos.list method to retrieve the video resource.
    $listResponse = $youtube->videos->listVideos("status", array('id'=>$videoId));

#	array( 'id' => $VIDEO_ID, 'status' => array('privacyStatus' => 'public')));

    // If $listResponse is empty, the specified video was not found.
    if (empty($listResponse)) {
      $htmlBody .= sprintf('<h3>Can\'t find a video with video id: %s</h3>', $videoId);
    } else {
      // Since the request specified a video ID, the response only
      // contains one video resource.
      $video = $listResponse[0];
	$videoStatus = $video['status'];
	$videoStatus->privacyStatus = 'public'; #privacyStatus options are public, private, and unlisted
	$video->setStatus($videoStatus);
	$updateResponse = $youtube->videos->update('status', $video);


#    $htmlBody .= "<h3>Video Updated</h3><ul>";
#    $htmlBody .= sprintf('<li>Tags "%s" and "%s" added for video %s (%s) </li>',
#        array_pop($responseTags), array_pop($responseTags),
#        $videoId, $video['snippet']['title']);
#    $htmlBody .= '</ul>';
$htmlBody = "We're Good!"; #Just a debug phrase to know that the script completed successfully. Not required to output

  }
    } catch (Google_Service_Exception $e) {
      $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
          htmlspecialchars($e->getMessage()));
    } catch (Google_Exception $e) {
      $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
          htmlspecialchars($e->getMessage()));
    }

    $_SESSION['token'] = $client->getAccessToken();
    } else {
      // If the user hasn't authorized the app, initiate the OAuth flow
      $state = mt_rand();
      $client->setState($state);
      $_SESSION['state'] = $state;

      $authUrl = $client->createAuthUrl();
      $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
    }
#      echo "<body>$htmlBody</body>";
}}
	?>
