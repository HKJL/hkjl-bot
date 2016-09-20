<?php

require_once __DIR__ . '/vendor/simple_html_dom.php';

function google($args) {

    // Make a HTTP request to Google as if we were a regular browser
    $curl_handle=curl_init();
    curl_setopt($curl_handle,CURLOPT_URL,'http://www.google.com/search?q='.urlencode($args).'&ie=utf-8&oe=utf-8&client=firefox-b');
    curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($curl_handle,CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:48.0) Gecko/20100101 Firefox/48.0');
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl_handle,CURLOPT_FOLLOWLOCATION,1);
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);

    // Carve the data we need out of the HTML DOM$
    $html = str_get_html($buffer);

    if($html->find('div.rc',0)) {
        $title = $html->find('div.rc',0)->find('h3',0)->plaintext;
        $text = $html->find('div.rc',0)->find('span.st',0)->plaintext;
        $link = $html->find('div.rc',0)->find('h3.r',0)->find('a',0)->href;

        // Convert stuff like &nbsp; to proper chars
        $input = html_entity_decode($title." - ".$link." - ".$text);

        // Convert stuff like &#39; to proper chars
        $output = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $input);

    } else {
        $output = "Geen resultaten...";
    }

    return "[Google] ".$output;

}