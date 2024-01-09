<?php

namespace App\Services\Scraper\Stores;

use App\Services\Scraper\Panther;
use App\Services\Scraper\Curl;

/**
 * Class BaseStore
 *
 * An abstract class providing a foundation for store-specific scraper implementations.
 * Implements common functionality and enforces the Scrapeable interface.
 */
abstract class BaseStore implements Scrapeable
{
    /**
     * The Curl instance for making HTTP requests and handling responses.
     *
     * @var Curl
     */
    protected $curl;

    /**
     * The Panther instance for making HTTP requests and handling responses.
     *
     * @var Panther
     */
    protected $panther;

    /**
     * BaseStore constructor.
     *
     * Initializes the Curl instance through dependency injection.
     */
    public function __construct()
    {
        if ($this->curl) $this->curl = app(Curl::class);
        if ($this->panther) $this->panther = app(Panther::class);
    }

    /**
     * Static method to get the store name.
     *
     * @return string
     */
    public static function getStoreName()
    {
        return static::NAME;
    }

    /**
     * Retrieves the XPath instance from the Curl instance for parsing HTML content.
     *
     * @return mixed
     */
    protected function getXPath()
    {
        return $this->curl->getXPath();
    }
}