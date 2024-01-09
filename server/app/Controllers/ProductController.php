<?php

namespace App\Controllers;

use Framework\Http\Request;

use App\Models\Product;
use App\Models\UserProduct;

use App\Controllers\UserProductController;
use App\Controllers\ScrapController;

/**
 * Class ProductController
 *
 * Handles the creation and management of products, including scraping information from provided URLs.
 */
class ProductController extends Controller
{
    private function removeQueryAndFragment($url) {
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['scheme'], $parsedUrl['host'], $parsedUrl['path'])) {
            $cleanUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $parsedUrl['path'];

            $cleanUrl = rtrim($cleanUrl, '/') . '/';

            return $cleanUrl;
        }

        return false;
    }

    /**
     * Store a newly created product in the database and associate it with a user.
     *
     * @param Request $request
     * @return \Framework\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|string',
            'price' => 'required|decimal:0,2'
        ]);

        $validated['url'] = $this->removeQueryAndFragment($validated['url']);

        $scrapController = new ScrapController();

        if (!$scrapController->isUrlValid($validated['url'])) return $this->errorResponse("Invalid URL provided. Please provide a valid URL.");

        if (!$product = Product::where('url', $validated['url'])->first()) {
            $product = Product::create($validated);

            $isProductFetched = $scrapController->scrapProduct($product);

            if (!$isProductFetched) {
                $product->delete();
                return $this->errorResponse("It seems we couldn't fetch this product. Please try again later or provide another URL.");
            }
        }

        if (UserProduct::where('product_id', $product->id)->where('user_id', $request->user()->id)->first()) return $this->errorResponse("You are already observing this product. There's no need to add it again.");

        $userProduct = (new UserProductController)->store($request, [
            'product_id' => $product->id,
            'price' => $validated['price']
        ]);

        return $this->successResponse("Product added successfully.", ['product' => $userProduct]);
    }
}