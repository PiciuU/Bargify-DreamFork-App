<?php

namespace App\Services\Scraper;

/**
 * Class Curl
 *
 * Handles HTTP requests using cURL and provides methods for document parsing.
 */
class Curl
{
    /**
     * User agent string for cURL requests.
     *
     * @var string|null
     */
    private $HTTP_USER_AGENT = null;

    /**
     * cURL options for requests.
     *
     * @var array
     */
    private $CURL_OPTIONS = [];

    /**
     * Result of the cURL request.
     *
     * @var string|null
     */
    private $CURL_RESULT = null;

    /**
     * Cookies for the cURL request.
     *
     * @var string|null
     */
    private $COOKIES = null;

    /**
     * DOMDocument for parsing HTML.
     *
     * @var \DomDocument|null
     */
    private $DOCUMENT = null;

    /**
     * DOMXPath for navigating the DOMDocument.
     *
     * @var \DOMXPath|null
     */
    private $XPATH = null;

    /**
     * Constructor for the Curl class.
     *
     * Initializes cURL options and sets the user agent.
     */
    public function __construct() {
        $this->HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
        $this->CURL_OPTIONS = [
            CURLOPT_CUSTOMREQUEST  =>"GET",
            CURLOPT_POST           => false,
            CURLOPT_USERAGENT      => $this->HTTP_USER_AGENT,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_MAXREDIRS      => 1,
        ];
    }

    /**
     * Fetches the website content using cURL.
     *
     * @param string $url - The URL of the website to fetch.
     */
    public function fetchWebsite($url) {
        $curl_connection = curl_init($url);
        curl_setopt_array($curl_connection, $this->CURL_OPTIONS);
        if ($this->COOKIES != null) curl_setopt($curl_connection, CURLOPT_COOKIE, $this->COOKIES);
        $this->CURL_RESULT = curl_exec($curl_connection);
        curl_close($curl_connection);

        $this->setDocument();
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