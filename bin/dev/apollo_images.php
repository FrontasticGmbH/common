<?php
fetch_all('https://www.apollo.de');


function fetch_all($url)
{
    if (null === ($html = fetch_page($url))) {
        return;
    }

    foreach (extract_links($url, $html) as $link) {
        extract_images($url, $html);
        fetch_all($link);
    }
}

function fetch_page($url)
{
    static $done = [];
    if (isset($done[$url])) {
        return null;
    }
    $done[$url] = $url;

    fwrite(STDERR, "Open: {$url}\n");
    return @file_get_contents($url);
}

function extract_links($url, $html)
{
    $schema = parse_url($url, PHP_URL_SCHEME);
    $hostname = parse_url($url, PHP_URL_HOST);

    preg_match_all('(<a\s[^>]*href="([^"]+)")', $html, $matches);

    $links = [];
    foreach ($matches[1] as $match) {
        if (0 === strpos($match, '/')) {
            $links[] = sprintf("%s://%s%s", $schema, $hostname, $match);
        }
    }
    return array_map('html_entity_decode', $links);
}

function extract_images($url, $html)
{
    $schema = parse_url($url, PHP_URL_SCHEME);
    $hostname = parse_url($url, PHP_URL_HOST);

    preg_match_all('(<img\s[^>]*src="([^"]+/productImages/[^"]+)")', $html, $matches);

    foreach ($matches[1] as $match) {
        if (0 === preg_match('(/(\d+)-(Angle|Front)-)iU', $match, $sku)) {
            continue;
        }

        fwrite(STDERR, sprintf("%s => %s://%s%s\n", $sku[1], $schema, $hostname, $match));
        printf("%s\t%s://%s%s\n", $sku[1], $schema, $hostname, $match);
    }
}
