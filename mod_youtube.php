<?php

// Reference: https://github.com/youtube/api-samples/blob/master/php/search.php

require_once __DIR__ . '/vendor/autoload.php';

function youtube($args) {

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
