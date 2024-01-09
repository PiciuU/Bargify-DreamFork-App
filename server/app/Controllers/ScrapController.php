<?php

namespace App\Controllers;

use Framework\Http\Request;

use Carbon\Carbon;

use App\Models\Product;

use App\Services\Scraper\Scraper;

use App\Controllers\NotificationController;

/**
 * Class ScrapController
 *
 * Handles product scraping and related functionalities.
 */
class ScrapController extends Controller
{
    /**
     * @var Scraper Instance of the Scraper service.
     */
    private $scraper;

    /**
     * ScrapController constructor.
     */
    public function __construct()
    {
        $this->scraper = new Scraper();
    }

    /**
     * Checks if the given URL is valid and belongs to a supported store.
     *
     * @param string $url The URL to be validated.
     * @return bool
     */
    public function isUrlValid($url)
    {
        return $this->scraper->isUrlValid($url);
    }

    /**
     * Scrapes product information and updates the database.
     *
     * @param Product $product The product to be scraped.
     * @return bool Returns true if scraping and updating are successful, false otherwise.
     */
    public function scrapProduct($product)
    {
        if (!$this->scraper->fetchWebsite($product->url) || !$this->scraper->getProductName()) return false;

        $isProductAvailable = $this->scraper->isProductAvailable();

        $product->update([
            'name' => $this->scraper->getProductName(),
            'image' => $this->scraper->getProductImage(),
            'last_known_price' => $this->scraper->getProductPrice(),
            'is_available' => $isProductAvailable,
            'last_available_at' => $isProductAvailable ? Carbon::now() : null
        ]);

        return true;
    }

    /**
     * Retrieves scraper settings, including available store names and the schedule.
     *
     * @param Request $request The HTTP request.
     * @return mixed Returns the response with settings information.
     */
    public function settings(Request $request)
    {
        $roundedDateTime = Carbon::now()->ceil('5 minutes')->add('30 seconds')->toDateTimeString();

        return $this->successResponse("Settings fetched.", ['stores' => $this->scraper->getAvailableStoresNames(), 'schedule' => $roundedDateTime]);
    }

    /**
     * Scrapes all products in the database.
     *
     * @param Request $request The HTTP request.
     * @return bool Returns true if the scraping process is successful, false otherwise.
     */
    public function scrapAll(Request $request)
    {
        if (!$request->get('secret') || $request->get('secret') !== env('SCRAPER_SECRET_KEY')) {
            return $this->errorResponse("Unauthorized. Please provide a valid secret key.");
        }

        $products = Product::all();

        foreach ($products as $product) {
            $this->scrapOne($request, $product->id);
        }
    }

    /**
     * Scrapes a specific product.
     *
     * @param Request $request The HTTP request.
     * @param int $productId The ID of the product to be scraped.
     * @return bool Returns true if scraping is successful, false otherwise.
     */
    public function scrapOne(Request $request, $productId)
    {
        if (!$request->get('secret') || $request->get('secret') !== env('SCRAPER_SECRET_KEY')) {
            return $this->errorResponse("Unauthorized. Please provide a valid secret key.");
        }

        if (!$product = Product::find($productId)) return false;


        if (!$this->isUrlValid($product->url)) return false;

        if (!$this->scraper->fetchWebsite($product->url)) return false;

        $isProductAvailable = $this->scraper->isProductAvailable();

        $productPreviousPrice = $product->last_known_price;
        $productWasPreviouslyAvailable = $product->is_available;

        $product->update([
            'name' => $this->scraper->getProductName(),
            'image' => $this->scraper->getProductImage(),
            'last_known_price' => $this->scraper->getProductPrice(),
            'is_available' => $isProductAvailable,
            'last_available_at' => $isProductAvailable ? Carbon::now() : $product->last_available_at
        ]);

        if (!$product->is_available || !$product->name) return true;

        $notificationController = new NotificationController();

        $productBecameAvailable = !$productWasPreviouslyAvailable && $product->is_available;
        $hasPriceDrop = $this->scraper->getProductPrice() < $productPreviousPrice;

        if ($productBecameAvailable || $hasPriceDrop) {
            $notificationController->sendProductNotification($product);
        }

        return true;
    }
}