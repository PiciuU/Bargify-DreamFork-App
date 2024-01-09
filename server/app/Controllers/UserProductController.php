<?php

namespace App\Controllers;

use Framework\Http\Request;

use App\Models\UserProduct;
use App\Models\Product;

use Framework\Support\Facades\DB;
use Framework\Support\Facades\Validator;

/**
 * Class UserProductController
 *
 * Handles actions related to user products, such as observing, listing, updating, and deleting user's observed products.
 */
class UserProductController extends Controller
{
    /**
     * Get product details for a specific user and product combination.
     *
     * @param int $user_id
     * @param int $product_id
     * @return mixed
     */
    private function product($user_id, $product_id)
    {
        $result = DB::table('user_products as up')
            ->join('products as p', 'up.product_id', '=', 'p.id')
            ->where('up.product_id', $product_id)->where('up.user_id', $user_id)
            ->select(['p.id', 'p.name', 'p.url', 'p.is_available', 'p.last_known_price', 'p.last_available_at', 'up.max_price', 'up.enable_notifications', 'p.updated_at'])
            ->first();

        return $result;
    }

    /**
     * Get a list of products for a specific user.
     *
     * @param int $user_id
     * @return mixed
     */
    private function products($user_id)
    {
        $result = DB::table('user_products as up')
            ->join('products as p', 'up.product_id', '=', 'p.id')
            ->where('up.user_id', $user_id)
            ->select(['p.id', 'p.name', 'p.url', 'p.is_available', 'p.last_known_price', 'p.last_available_at', 'up.max_price', 'up.enable_notifications', 'p.updated_at'])
            ->get();

        return $result;
    }

    /**
     * Store a new user product.
     *
     * @param Request $request
     * @param array $data
     * @return mixed
     */
    public function store(Request $request, $data)
    {
        if (!$userProduct = UserProduct::create([
            'user_id' => $request->user()->id,
            'product_id' => $data['product_id'],
            'max_price' => $data['price'],
        ])) {
            return $this->errorResponse('Failed to observe the product. Please try again.');
        }
        return $this->product($request->user()->id, $userProduct->product_id);
    }

    /**
     * Get a list of all user products.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if (!$userProducts = $this->products($request->user()->id)) return $this->errorResponse('Failed to fetch user products.');

        return $this->successResponse('User products fetched successfully.', ['products' => $userProducts]);
    }

    /**
     * Get details of a specific user product.
     *
     * @param Request $request
     * @param int $product_id
     * @return mixed
     */
    public function show(Request $request, $product_id)
    {
        if (!$userProduct = $this->product($request->user()->id, $product_id)) return $this->errorResponse('Failed to fetch user product details.');

        return $this->successResponse('User product details fetched successfully.', ['product' => $userProduct]);
    }

    /**
     * Update an existing user product.
     *
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'max_price' => 'required|decimal:0,2',
            'enable_notifications' => 'required|integer|boolean'
        ]);

        if (!$userProduct = UserProduct::where('product_id', $validated['product_id'])->where('user_id', $request->user()->id)->first()) return $this->errorResponse('User product does not exist.');

        $userProduct->update([
            'max_price' => $validated['max_price'],
            'enable_notifications' => $validated['enable_notifications']
        ]);

        $userProduct = $this->product($request->user()->id, $userProduct->product_id);

        return $this->successResponse('User product updated successfully.', ['product' => $userProduct]);
    }

    /**
     * Delete an existing user product.
     *
     * @param Request $request
     * @param int $product_id
     * @return mixed
     */
    public function delete(Request $request, $product_id)
    {
        $validator = Validator::make(['product_id' => $product_id], [
            'product_id' => 'required|integer'
        ]);

        if ($validator->fails()) $validator->throw();

        $validated = $validator->validated();

        if (!UserProduct::where('product_id', $validated['product_id'])->where('user_id', $request->user()->id)->delete()) return $this->errorResponse('Failed to delete user product.');

        $productUsers = UserProduct::where('product_id', $validated['product_id'])->count();

        if ($productUsers == 0) Product::find($validated['product_id'])->delete();

        return $this->successResponse('User product deleted successfully.');
    }
}