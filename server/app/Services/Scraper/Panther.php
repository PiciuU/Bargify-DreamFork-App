<?php

namespace App\Services\Scraper;

use Symfony\Component\Panther\Client;

/**
 * Class Panther
 *
 * Handles HTTP requests using cURL and provides methods for document parsing.
 */
class Panther
{
    private $client;

    public function __construct()
    {
        $this->client = Client::createChromeClient(base_path() . '/drivers/chromedriver.exe', null, [
            'chromedriver_arguments' => [
                '--log-path='.storage_path().'/logs/panther.log',
                '--log-level=DEBUG',
            ],
            'capabilities' => [
                'goog:loggingPrefs' => [
                    'browser' => 'ALL', // calls to console.* methods
                    'performance' => 'ALL', // performance data
                ],
            ],
        ]);
    }

    public function getClient()
    {
        return $this->client;
    }
    /**
     * Fetches the website content using cURL.
     *
     * @param string $url - The URL of the website to fetch.
     */
    public function fetchWebsite($url) {
        $client = Client::createChromeClient(base_path() . '/drivers/chromedriver.exe', null, [
            'chromedriver_arguments' => [
                '--log-path='.storage_path().'/logs/panther.log',
                '--log-level=DEBUG',
            ],
            'capabilities' => [
                'goog:loggingPrefs' => [
                    'browser' => 'ALL', // calls to console.* methods
                    'performance' => 'ALL', // performance data
                ],
            ],
        ]);

        $consoleLogs = $client->getWebDriver()->manage()->getLog('browser');

        return $client;
    }

    /**
     * Parses the cURL result and sets the DOMDocument and DOMXPath.
     */
    private function setDocument() {
        $this->DOCUMENT = new \DomDocument('1.0', 'UTF-8');
        $this->DOCUMENT->loadHTML($this->CURL_RESULT, LIBXML_NOERROR);
        $this->XPATH = new \DOMXPath($this->DOCUMENT);
    }

    /**
     * Sets cookies for the cURL request.
     *
     * @param string $cookies - The cookies to set.
     */
    public function setCookies($cookies) {
        $this->COOKIES = $cookies;
    }

    /**
     * Gets the result of the cURL request.
     *
     * @return string|null - The result of the cURL request.
     */
    public function getResult()
    {
        return $this->CURL_RESULT;
    }

    /**
     * Gets the cookies used in the cURL request.
     *
     * @return string|null - The cookies used in the cURL request.
     */
    public function getCookies()
    {
        return $this->COOKIES;
    }

    /**
     * Gets the DOMDocument for the parsed HTML.
     *
     * @return \DomDocument|null - The DOMDocument for the parsed HTML.
     */
    public function getDocument()
    {
        return $this->DOCUMENT;
    }

    /**
     * Gets the DOMXPath for navigating the DOMDocument.
     *
     * @return \DOMXPath|null - The DOMXPath for navigating the DOMDocument.
     */
    public function getXPath()
    {
        return $this->XPATH;
    }
}