<?php

namespace spartaksun\sitemap\generator\loader;


use GuzzleHttp\Client;

class Loader
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $userAgent = 'Sitemap Generator';


    /**
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     * @throws LoaderException
     */
    public function load()
    {
        $client = new Client();

        try {

            $response = $client->get($this->url, [
                'headers' => [
                    'User-Agent' => $this->userAgent,
                ]
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