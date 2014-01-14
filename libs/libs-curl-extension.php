<?php
function curl_redirect_exec($ch, &$redirects, $curlopt_header = false) {
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code == 301 || $http_code == 302) {
        list($header) = explode("\r\n\r\n", $data, 2);
        $matches = array();
        preg_match("/(Location:|URI:)[^(\n)]*/", $header, $matches);
        $url = trim(str_replace($matches[1], "", $matches[0]));
        $last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $last_url_parsed = parse_url($last_url);
        $url_parsed = parse_url($url);
        if (isset($url_parsed)) {
            if(!isset($url_parsed['host'])){
                $url = sprintf("%s://%s",$last_url_parsed['scheme'],$last_url_parsed['host']).$url;
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            $redirects++;
            return curl_redirect_exec($ch, $redirects, $curlopt_header);
        }
    }

    if ($curlopt_header) {
        return $data;
    } else {
        list(, $body) = explode("\r\n\r\n", $data, 2);
        return $body;
    }
}