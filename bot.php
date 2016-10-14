<?php

include("mod_youtube.php");
include("mod_discourse.php");
include("mod_weather.php");
include("mod_httpstatus.php");
include("mod_imdb.php");
include("mod_google.php");
include("mod_wolfram.php");
include("mod_karma.php");
include("mod_tld.php");
include("mod_info.php");
include("mod_quote.php");

$action = $_GET['action'];
$args = $_GET['args'];

// Weird chars being added by python script? 
$args = str_replace("%0D%0A","",$args);

// Remove any extra whitespace around the incoming data before parsing
$args = trim(preg_replace('/\s\s+/', ' ', $args));

switch($action)
{
    case 'version':
        output("HKJL Bot - Eigenaar: Sling - Help: !help");
        break;
    case 'base64encode':
        output(base64_encode($args));
        break;
    case 'base64decode':
        output(base64_decode($args));;
        break;	
    case 'imdb':
        output(imdb($args));
        break;
    case 'discourse':
        output(discourse_search($args));
        break;
    case 'google':
        output(google($args));
        break;
    case 'lmgtfy':
        output("http://lmgtfy.com/?q=".urlencode($args));
        break;
    case 'help':
        output("Handleiding van deze bot: https://community.hackenkunjeleren.nl/t/handleiding-hkjl-irc-bot/199");
        break;
    case 'wolfram':
        output(wolfram($args));
        break;
    case 'yt':
    case 'youtube':
        output(youtube($args));
        break;
    case 'md5':
        output(md5($args));
        break;
    case 'echo':
        output($args);
        break;
    case 'bin2hex':
        output(dechex(bindec($args)));
        break;
    case 'reverse':
        output(strrev($args));
        break;
    case 'rot13':
        output(str_rot13($args));
        break;
    case 'ascii2bin':
        for($i = 0; $i != strlen($args); $i++) {
            $value = unpack('H*', $args[$i]);
            $output .= base_convert($value[1], 16, 2).' ';
        }
        output($output);
        break;
    case 'ascii2oct':
        for($i = 0; $i != strlen($args); $i++) {
            $value = unpack('H*', $args[$i]);
            $output .= base_convert($value[1], 16, 8).' ';
        }
        output($output);
        break;
    case 'ascii2dec':
        for($i = 0; $i != strlen($args); $i++) {
            $output .= ord($args[$i]).' ';
        }
        output($output);
        break;
    case 'ascii2hex':
        for($i = 0; $i != strlen($args); $i++) {
            $output .= dechex(ord($args[$i])).' ';
        }
        output($output);
        break;
    case 'levenshtein':
        $args = explode(' ',$args,2);
        output(levenshtein($args[0],$args[1]));
        break;
    case 'dice':
    case 'dobbelsteen':
        output("The dice rolls.... ".rand(1,6));
        break;
    case 'w':
    case 'weer':
        output(weather($args));
        break;
    case 'httpbanner':
        output(http_banner($args));
        break;
    case 'httpstatus':
        output(httpstatus($args));
        break;
    case 'minutecron':
        output(discourse_latest());
        break;
    case 'gettitle':
        output(get_title($args));
        break;
    case 'tld':
        output(tld($args));
        break;
    case 'karma':
    case 'karma?':
        output(karmalookup($args));
        break;
    case 'karma+':
        output(karmaup($args));
        break;
    case 'karma-':
        output(karmadown($args));
        break;
    case 'info':
    case 'info?':
        output(getinfo($args));
        break;
    case 'info-':
        output(delinfo($args));
        break;
    case 'info+':
        output(setinfo($args));
        break;
    case 'kies':
        output(choose($args));
        break;
    case 'quote+':
        output(addquote($args));
        break;
    case 'quote-':
        output(delquote($args));
        break;
    case 'quote#':
        output(getquote($args));
        break;
    case 'quote':
        output(randomquote());
        break;
    case 'shrug':
        output("¯\_(ツ)_/¯");
        break;
    default:
        // Silently ignore invalid actions to prevent unwanted spamming

        // Handle shorthand karma commands separately, since they are normally parsed as action and args:
        if(endsWith($args,"--")) {                              // Example: !two words--
            output(karmadown(substr($action." ".$args,0,-2)));
        } else if(endsWith($args,"++")) {                       // Example: !two words++
            output(karmaup(substr($action." ".$args,0,-2)));
        } else if(endsWith($action,"--")) {                     // Example: !word--
            output(karmadown(substr($action,0,-2)));
        } else if(endsWith($action,"++")) {                     // Example: !word++
            output(karmaup(substr($action,0,-2)));
        }

        break;
}

function get_title($args) {

    // If user did not supply a HTTP or HTTPS scheme, add a HTTP scheme.
    if(strpos($args,"http://")!==0 && strpos($args,"https://")!==0) {
        $args = "http://".$args;
    }

    // Take the first 'word' from the supplied input
    $url = explode(" ", $args)[0];
    // Remove chars that are not supposed to be in a URL
    $url = filter_var($url, FILTER_SANITIZE_URL);

    // Check if what remains is a valid URL
    if(filter_var($url, FILTER_VALIDATE_URL)) {
        // Fetch the contents of the URL, and limit http body length to mitigate some possible abuse
        $content = file_get_contents($url,false,NULL,0,65536);

        // Replace any additional whitespace and newlines so we can use regex and not run into multiline texts
        $content = trim(preg_replace('/\s+/', ' ', $content));
        // Use a regex to parse for the title. We're not using DOM here since our input could be truncated malformed HTML
        if(preg_match("/\<title\>(.*)\<\/title\>/i",$content,$title)) {
            return "[Title] ".$title[1];
        } else {
            return "";
        }
    } else {
        return "";
    }
}

function http_banner($args) {
    // If user did not supply a HTTP or HTTPS scheme, add a HTTP scheme.
    if(strpos($args,"http://")!==0 && strpos($args,"https://")!==0) {
        $args = "http://".$args;
    }

    // We're going to make a HEAD request, instead of the default GET, since we're not interested in a response body.
    stream_context_set_default(array('http'=>array('method'=>'HEAD')));

    // If the user gave us a valid URL, retrieve the HTTP headers.
    if(filter_var($args, FILTER_VALIDATE_URL)) {
        $headers = get_headers($args);
        if(!$headers) {
            return "This host looks down to me...";
        } else {
            return array_values(array_filter($headers, function($v) { return strpos($v, 'Server:') !== false; }))[0];
        }
    } else {
        return "That doesn't look like a valid URL...";
    }
}

function choose($args) {

    $choices = explode(',', $args);

    if(count($choices)==1) {
        return "[kies] Ben je kort ofzo? Geef me meerdere opties, gescheiden door komma's.";
    } else {
        return trim($choices[array_rand($choices)]);
    }

}

function output($output) {
    // Remove any spaces around output and remove non-printable characters such as LF, CR, SOH, etc.
    $output = trim($output);
    $output = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $output);

    // Limit output length
    if(strlen($output) > 450) {
      $output = substr($output,0,445)."(...)";
    }

    echo htmlspecialchars($output);
}

function endsWith($haystack, $needle) {
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}
