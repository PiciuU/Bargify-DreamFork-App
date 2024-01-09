<?php

namespace App\Services\Scraper;

/**
 * Class Scraper
 *
 * Manages the scraping functionality for supported stores.
 * Handles URL validation, store resolution, and dynamic method invocation on the resolved store.
 */
class Scraper
{
    /**
     * List of store classes
     *
     * @var array
     */
    private $stores = [
        Stores\Morele::class,
        Stores\Komputronik::class,
        // Stores\XKom::class
    ];

    /**
     * Object of the currently resolved store
     *
     * @var object|null
     */
    private $resolver;

    /**
     * Checks if the given URL is valid and belongs to a supported store
     *
     * @param string $url
     * @return bool
     */
    public function isUrlValid($url)
    {
        $isUrlValid = filter_var($url, FILTER_VALIDATE_URL) !== false;

        $isStoreValid = false;

        foreach ($this->getStores() as $store) {
            if (strpos($url, $store::getStoreName()) !== false) {
                $isStoreValid = true;
                $this->resolve($store);
                break;
            }
        }

        return $isUrlValid && $isStoreValid;
    }

   /**
     * Gets the list of supported stores
     *
     * @return array
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * Gets the names of available stores
     *
     * @return array
     */
    public function getAvailableStoresNames()
    {
        return array_map(fn($store) => $store::getStoreName(), $this->getStores());
    }

    /**
     * Resolves a store based on the class and creates an object of the resolved store
     *
     * @param string $store
     */
    public function resolve($store)
    {
        $this->resolver = new $store;
    }

    /**
     * Magic method enabling dynamic invocation of methods on the resolved store
     *
     * @param string $method
     * @param array $parameters
     * @return mixed|null
     */
    public function __call($method, $parameters)
    {
        if ($this->resolver) {
            return $this->resolver->$method(...$parameters);
        }

        return null;
    }
}