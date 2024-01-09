<?php

namespace App\Services\Scraper\Stores;

/**
 * Class Morele
 *
 * Represents a store-specific implementation for Morele.net.
 * Extends the BaseStore class for common functionality.
 */
class Morele extends BaseStore
{
    /**
     * Store name constant.
     */
    const NAME = 'morele.net';

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
        $this->injectCookies();

        return $this->fetchProduct();
    }

    /**
     * Injects cookies into the cURL instance based on the website response.
     */
    private function injectCookies()
    {
        $matches = [];
        preg_match_all('/^Set-Cookie:\s*(?P<cookie>[^;]*)/mi', $this->curl->getResult(), $matches);

        $sessid = $matches['cookie']['PHPSESSID'] ?? null;
        $cart = $matches['cookie']['cart'] ?? null;

        if ($sessid && $cart) {
            $this->curl->setCookies("PHPSESSID=$sessid;cart=$cart");
        }
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
        $element = $this->getXPath()->query("//h1[@class='prod-name']")->item(0);
        return $element ? ucfirst($this->getStoreName()) . ' | ' . $element->getAttribute('data-default') : null;
    }

    /**
     * Fetches the product price from the website.
     *
     * @return float The product price or 0 if not found.
     */
    private function fetchProductPrice()
    {
        return $this->getXPath()->query("//div[@class='product-price']")->item(0)?->getAttribute('data-price') ?? 0;
    }

    /**
     * Fetches the product image from the website.
     *
     * @return float The product image or null if not found.
     */
    private function fetchProductImage()
    {
        return $this->getXPath()->query("//img[@itemprop='image']")->item(0)?->getAttribute('data-src') ?? null;
    }

    /**
     * Fetches the product availability status from the website.
     *
     * @return bool True if the product is available, false otherwise.
     */
    private function fetchProductAvailability()
    {
        return $this->getXPath()->query("//link[@itemprop='availability']")->item(0)?->getAttribute('href') == 'http://schema.org/InStock';
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