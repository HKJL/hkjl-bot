<?php

function manpage($args) {
    $url_format = 'http://man7.org/linux/man-pages/man%d/%s.%d.html';
    $begin_tag  = '<span class="top-link">top</span></a></h2><pre>';

    $a = explode(' ', $args);
    if (sizeof($a) != 2)
        return "usage: man [SECTION] [MANPAGE]";


    $section = intval($a[0]);
    $manpage = urlencode($a[1]);

    if ($section < 1 || $section > 9)
        return "invalid section";

    $url = sprintf($url_format, $section, $manpage, $section);
    $content = file_get_contents($url);

    if ($content) {
        $begin = strpos($content, $begin_tag) + strlen($begin_tag);
        $end = strpos($content, "\n\n", $begin);
        $lines = explode("\n", html_entity_decode(strip_tags(substr($content, $begin, $end))));
        $desc = array_slice(
                    array_filter(
                        $lines,
                        function($v) { return strlen($v) && $v[0] == " "; }
                    ), 0, 3);
        return $url . " -> " . join("   ~~~   ", array_map(function($v) { return trim($v); }, $desc));
    } else {
        return "manpage not found";
    }
}
