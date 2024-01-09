<?php

namespace App\Services\Scraper\Stores;

/**
 * Class XKom
 *
 * Represents a store-specific implementation for x-kom.pl.
 * Extends the BaseStore class for common functionality.
 */
class XKom extends BaseStore
{
    /**
     * Store name constant.
     */
    const NAME = 'x-kom.pl';

    /**
     * Enable specified scrapper
     */
    protected $panther = true;

    /**
     * Holds the product information fetched from the website.
     *
     * @var array
     */
    protected $product = [];

    /**
     * Fetches the website content and extracts product information.
     *
     * @param string $url The URL of the product page.
     * @return bool True if product information is successfully fetched, false otherwise.
     */
    public function fetchWebsite($url)
    {
        $client = $this->panther->getClient();

        $client->request('GET', $url);
        $cookieJar = $client->reload();

        $client->getCrawler()->filter('.sc-n4n86h-1')->each(function ($node) {
           //
        });

        return $this->fetchProduct();
    }

    /**
     * Fetches the product information from the website.
     *
     * @return bool True if product information is successfully fetched, false otherwise.
     */
    private function fetchProduct()
    {
        $name = $this->fetchProductName();
        if (!$name) return false;

        $this->product = [
            'name' => $name,
            'price' => $this->fetchProductPrice(),
            'image' => $this->fetchProductImage(),
            'available' => $this->fetchProductAvailability(),
        ];

        return true;
    }

    /**
     * Fetches the product name from the website.
     *
     * @return string|null The product name or null if not found.
     */
    private function fetchProductName()
    {

    }

    /**
     * Fetches the product price from the website.
     *
     * @return float The product price or 0 if not found.
     */
    private function fetchProductPrice()
    {

    }

    /**
     * Fetches the product image from the website.
     *
     * @return float The product image or null if not found.
     */
    private function fetchProductImage()
    {

    }

    /**
     * Fetches the product availability status from the website.
     *
     * @return bool True if the product is available, false otherwise.
     */
    private function fetchProductAvailability()
    {

    }

    /**
     * Retrieves the product name.
     *
     * @return string|null The product name or null if not available.
     */
    public function getProductName()
    {
        return $this->product ? $this->product['name'] : null;
    }

    /**
     * Retrieves the product image.
     *
     * @return string|null The product image or null if not available.
     */
    public function getProductImage()
    {
        return $this->product ? $this->product['image'] : null;
    }

    /**
     * Retrieves the product price.
     *
     * @return float The product price or 0 if not available.
     */
    public function getProductPrice()
    {
        return $this->product ? $this->product['price'] : 0;
    }

    /**
     * Checks if the product is available.
     *
     * @return bool True if the product is available, false otherwise.
     */
    public function isProductAvailable()
    {
        return $this->product && $this->product['available'];
    }

}