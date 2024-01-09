<?php

namespace App\Services\Scraper\Stores;

/**
 * Class Komputronik
 *
 * Represents a store-specific implementation for Komputronik.pl.
 * Extends the BaseStore class for common functionality.
 */
class Komputronik extends BaseStore
{
    /**
     * Store name constant.
     */
    const NAME = 'komputronik.pl';

    /**
     * Enable specified scrapper
     */
    protected $curl = true;

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
        $this->curl->fetchWebsite($url);

        return $this->fetchProduct();
    }

    /**
     * Fetches the product information from the website.
     *
     * @return bool True if product information is successfully fetched, false otherwise.
     */
    private function fetchProduct()
    {
        $node = $this->getXPath()->query("//ktr-product")->item(0)?->getAttribute('gtm-product');

        if (!$node) return false;

        $node = json_decode($node, true);

        $this->product = [
            'name' => $this->fetchProductName($node),
            'price' => $this->fetchProductPrice($node),
            'image' => $this->fetchProductImage($node),
            'available' => $this->fetchProductAvailability($node),
        ];

        return true;
    }

    /**
     * Fetches the product name from the website.
     *
     * @return string|null The product name or null if not found.
     */
    private function fetchProductName($node)
    {
        return $node['item_name'] ? ucfirst($this->getStoreName()) . ' | ' . $node['item_name'] : null;
    }

    /**
     * Fetches the product price from the website.
     *
     * @return float The product price or 0 if not found.
     */
    private function fetchProductPrice($node)
    {
        return $node['price'] ?? 0;
    }

    /**
     * Fetches the product image from the website.
     *
     * @return float The product image or null if not found.
     */
    private function fetchProductImage($node)
    {
        return $this->getXPath()->query('//meta[@property="og:image"]/@content')->item(0)?->nodeValue ?? null;
    }

    /**
     * Fetches the product availability status from the website.
     *
     * @return bool True if the product is available, false otherwise.
     */
    private function fetchProductAvailability($node)
    {
        return $node['availability'] == 'available';
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