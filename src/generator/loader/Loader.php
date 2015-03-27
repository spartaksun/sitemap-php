<?php

namespace spartaksun\sitemap\generator\loader;


use GuzzleHttp\Client;

class Loader
{
    /**
     * @var string
     */
    private $userAgent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:36.0) Gecko/20100101 Firefox/36.0';

    /**
     * @return string
     * @throws LoaderException
     */
    public function load($url)
    {
        if (empty($url) || !is_string($url)) {
            throw new LoaderException('Empty url');
        }

        $client = new Client();

        try {

            $response = $client->get($url, [
                'headers' => [
                    'User-Agent' => $this->userAgent,
                ],
            ]);
            switch ($response->getStatusCode()) {
                case 200: {

                    $body = $response->getBody();
                    if (is_null($body)) {
                        throw new LoaderException('Body is null');
                    }

                    return $body->getContents();
                }
                    break;
                default:
                    throw new LoaderException('Incorrect status code:' . $response->getStatusCode());
            }
        } catch (\RuntimeException $e) {
            throw new LoaderException('RuntimeException: ' . $e->getMessage());
        }
    }
}