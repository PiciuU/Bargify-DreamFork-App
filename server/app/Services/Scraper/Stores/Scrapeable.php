<?php

namespace App\Services\Scraper\Stores;

/**
 * Interface Scrapeable
 *
 * An interface defining the contract for store scraper implementations.
 * Requires methods for fetching website content, checking product availability,
 * retrieving product name, and getting product price.
 */
interface Scrapeable
{
    /**
     * Fetches the content of the specified website.
     *
     * @param string $url The URL of the website to fetch.
     * @return mixed
     */
    public function fetchWebsite(string $url);

    /**
     * Checks whether the product is available.
     *
     * @return bool
     */
    public function isProductAvailable();

    /**
     * Retrieves the name of the product.
     *
     * @return mixed
     */
    public function getProductName();

    /**
     * Retrieves the price of the product.
     *
     * @return mixed
     */
    public function getProductPrice();
}
