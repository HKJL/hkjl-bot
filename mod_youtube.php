<?php

// Reference: https://github.com/youtube/api-samples/blob/master/php/search.php

require_once __DIR__ . '/vendor/autoload.php';

function youtube($args) {

  // Return random earlier mentioned youtube link when no search parameter was provided
  if(empty($args)) {
    include("sqlconfig.php");
    $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $stmt = $dbh->prepare("SELECT url FROM youtubelinks ORDER BY RAND() LIMIT 0,1");
    $stmt->execute();
    $result = $stmt->fetch();
    $link = "https://".$result['url'];
    return "[YT] ".$link." ".get_title($link,0);
  } else {

    // Load file containing $youtube_api_key value
    include("mod_youtube_config.php");   

    $client = new Google_Client();
    $client->setDeveloperKey($youtube_api_key);
    $youtube = new Google_Service_YouTube($client);

    try {
        $searchResponse = $youtube->search->listSearch('id,snippet', array('q' => $args, 'maxResults' => 1));

        if(count($searchResponse)>0) {
            foreach ($searchResponse['items'] as $searchResult) {
                switch ($searchResult['id']['kind']) {
                    case 'youtube#video':

                        // Insert link into database for later retrieval (see argument-less functionality at top of this module)
                        addYoutubeURL("www.youtube.com/watch?v=".$searchResult['id']['videoId']);

                        return sprintf('[YT] https://youtube.com/watch?v=%s - %s', $searchResult['id']['videoId'], $searchResult['snippet']['title']);
                        break;
                    case 'youtube#channel':
                        return sprintf('[YT] Kanaal https://www.youtube.com/channel/%s - %s', $searchResult['id']['channelId'], $searchResult['snippet']['title']);
                        break;
                    case 'youtube#playlist':
                        return sprintf('[YT] Afspeellijst https://www.youtube.com/playlist?list=%s - %s', $searchResult['id']['playlistId'], $searchResult['snippet']['title']);
                        break;
                    default:
                        return "[YT] Er gebeurde iets geks...";
                }
            }
        } else {
            return "[YT] Geen resultaten...";
        }
    } catch (Google_Service_Exception $e) {
        return "[YT] API Service Error: ".$e->getMessage();
    } catch (Google_Exception $e) {
        return "[YT] API Client Error: ".$e->getMessage();
    }
  }
}

function addYoutubeURL($url) {
  include("sqlconfig.php");
  $dbh = new PDO('mysql:host=localhost;dbname='.$db,$user,$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  $stmt = $dbh->prepare("INSERT INTO youtubelinks VALUES (:url)");
  $stmt->bindParam(":url", $url);
  $stmt->execute();
}
