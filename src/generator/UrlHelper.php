<?php

namespace spartaksun\sitemap\generator;


class UrlHelper
{

    /**
     * @param $url
     * @return string
     * @throws \ErrorException
     */
    public static function getMainPageUrl($url)
    {
        $parsed = self::parse($url);

        return "{$parsed['scheme']}://{$parsed['host']}";
    }

    /**
     * Parse url
     *
     * @param $url
     * @return array
     * @throws parser\ParserException
     */
    public static function parse($url)
    {
        $parsed = parse_url(trim($url));
        if (!empty($parsed['scheme']) && !empty($parsed['host'])) {
            return $parsed;
        }

        throw new parser\ParserException("Can not parse {$url}");
    }

    /**
     * Cast to full URL
     * @param array $urls
     * @param $mainPageUrl
     * @param $currentUrl
     * @return array
     * @throws \ErrorException
     */
    public static function normalizeUrls(array $urls, $mainPageUrl, $currentUrl)
    {
        $parsed = self::parse($mainPageUrl);

        $result = [];
        foreach ($urls as $url) {

            if (in_array($url, $result)) {
                continue;
            }

            $url = self::normalize($url, $mainPageUrl, $currentUrl);
            if (!$url) {
                continue;
            }

            if (self::isDomainAllowed($url, $parsed['host'])) {
                $result[] = $url;
            }
        }

        return $result;
    }

    /**
     * Cast to full URL
     * @param array $url
     * @param $mainPageUrl
     * @param $currentUrl
     * @return array
     */
    public static function normalize($url, $mainPageUrl, $currentUrl)
    {
        if(preg_match('~\.(jpg|jpeg|png|giff|tiff)$~i', $url)) {
            return false;
        }
        if (preg_match("~^http(|s):\/\/~", $url)) {
            return self::prepare($url);
        } elseif (preg_match("~^\/\/(.*)~", $url, $matches)) {
            return "http://{$matches[1]}";
        } elseif(preg_match('~javascript~', $url)){
            return false;
        } elseif (preg_match("~^\/~", $url)) {
            return self::prepare($mainPageUrl . $url);
        } elseif ($url == '/') {
            return self::prepare($mainPageUrl);
        } elseif (!preg_match('~mailto:~', $url)) {
            $pos = strrpos('/', $currentUrl, -0);
            if($pos !== false) {
                $base = substr($currentUrl, 0, $pos);
                return self::prepare($base . $url);
            } else {
                return self::prepare($mainPageUrl .'/'. $url);
            }

        } else {
            return false;
        }
    }

    /**
     * @param $url
     * @return string
     * @throws \ErrorException
     */
    public static function prepare($url)
    {
        $parsed = self::parse($url);

        $query = (!empty($parsed['query'])) ? "?{$parsed['query']}" : '';
        $path = (!empty($parsed['path'])) ? str_replace('//', '/', $parsed['path']) : '';
        $resultUrl = "{$parsed['scheme']}://{$parsed['host']}{$path}{$query}";

        return $resultUrl;
    }

    /**
     * Check if URL at the same domain
     * @param $url
     * @return bool
     * @throws \ErrorException
     */
    public static function isDomainAllowed($url, $host)
    {
        $parsed = self::parse($url);

        if ($parsed['host'] == $host) {
            return true;
        }

        return false;
    }

}