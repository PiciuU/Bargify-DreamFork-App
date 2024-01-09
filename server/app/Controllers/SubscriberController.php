<?php

namespace App\Controllers;

use Framework\Http\Request;

use App\Models\Subscriber;

/**
 * Class SubscriberController
 *
 * Handles subscription-related actions, including creation, updating, and deletion of subscriptions.
 */
class SubscriberController extends Controller
{
    /**
     * Create a new subscription.
     *
     * @param Request $request
     * @return \Framework\Http\Response
     */
    public function create(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required',
            'auth_token' => 'required',
            'public_key' => 'required'
        ]);

        if (!$subscriber = Subscriber::create([
            'user_id' => $request->user()->id,
            'endpoint' => $validated['endpoint'],
            'auth_token' => $validated['auth_token'],
            'public_key' => $validated['public_key']
        ])) {
            return $this->errorResponse('Failed to create subscription.');
        }

        return $this->successResponse("Subscription created successfully.");
    }

    /**
     * Update an existing subscription.
     *
     * @param Request $request
     * @return \Framework\Http\Response
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'old_endpoint' => 'required',
            'endpoint' => 'required',
            'auth_token' => 'required',
            'public_key' => 'required'
        ]);

        if (!$subscriber = Subscriber::where('endpoint', $validated['old_endpoint'])->where('user_id', $request->user()->id)->first()) {
            if (!$subscriber = Subscriber::create([
                'user_id' => $request->user()->id,
                'endpoint' => $validated['endpoint'],
                'auth_token' => $validated['auth_token'],
                'public_key' => $validated['public_key']
            ])) return $this->errorResponse('Failed to create subscription.');
        }

        $subscriber->update([
            'endpoint' => $validated['endpoint'],
            'auth_token' => $validated['auth_token'],
            'public_key' => $validated['public_key']
        ]);

        return $this->successResponse('Subscription updated successfully.');
    }

    /**
     * Delete an existing subscription.
     *
     * @param Request $request
     * @return \Framework\Http\Response
     */
    public function delete(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required',
        ]);

        if (!Subscriber::where('endpoint', $validated['endpoint'])->where('user_id', $request->user()->id)->delete()) return $this->errorResponse('Failed to delete subscription.');

        return $this->successResponse('Subscription deleted successfully.');
    }
}