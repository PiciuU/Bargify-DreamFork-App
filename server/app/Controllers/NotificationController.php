<?php

namespace App\Controllers;

use Framework\Http\Request;

use App\Models\Subscriber;
use App\Models\Product;
use App\Models\Notification;

use Carbon\Carbon;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription as PushSubscription;

/**
 * Class NotificationController
 *
 * Handles the sending of product availability notifications to subscribed users.
 */
class NotificationController extends Controller
{
    /**
     * The WebPush instance for sending push notifications
     *
     * @var WebPush
     */
    private $webPush;

    /**
     * NotificationController constructor.
     * Initializes the WebPush instance with VAPID authentication details.
     */
    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => env('VAPID_PUBLIC_KEY'),
                'privateKey' => env('VAPID_PRIVATE_KEY')
            ]
        ];

        $this->webPush = new WebPush($auth);
        $this->webPush->setAutomaticPadding(false);
    }

    /**
     * Retrieves subscribers who have opted in for notifications for a specific product.
     *
     * @param Product $product The product for which subscribers are retrieved
     * @return \Framework\Database\ORM\Collection The collection of subscribers
     */
    private function retrieveSubscribers(Product $product)
    {
        return Subscriber::select('subscribers.*')
            ->join('user_products as up', 'subscribers.user_id', '=', 'up.user_id')
            ->join('products as p', 'p.id', '=', 'up.product_id')
            ->where('up.product_id', $product->id)
            ->where('up.enable_notifications', 1)
            ->where('up.max_price', '>=', $product->last_known_price)
            ->get();
    }

    /**
     * Queues notifications for subscribers using their push subscription details and a payload.
     *
     * @param \Framework\Database\ORM\Collection $subscribers The collection of subscribers
     * @param array $payload The payload for the notification
     */
    private function queueNotifications($subscribers, $payload)
    {
        foreach ($subscribers as $subscriber) {
            $subscription = PushSubscription::create([
                'endpoint' => $subscriber->endpoint,
                'keys' => [
                    'p256dh' => $subscriber->public_key,
                    'auth' => $subscriber->auth_token
                ]
            ]);

            $this->webPush->queueNotification($subscription, json_encode($payload));
        }
    }

    /**
     * Flushes queued notifications and updates counters for successes, expired, and failed notifications.
     *
     * @param int $notification_successes Counter for successful notifications
     * @param int $notification_expired Counter for expired subscriptions
     * @param int $notification_failed Counter for failed notifications
     */
    private function flushNotifications(&$notification_successes, &$notification_expired, &$notification_failed)
    {
        foreach ($this->webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                $notification_successes += 1;
            } else if ($report->isSubscriptionExpired()) {
                $notification_expired += 1;
                $this->deleteExpiredSubscription($endpoint);
            } else {
                $notification_failed += 1;
            }
        }
    }

    /**
     * Deletes an expired subscription based on its endpoint.
     *
     * @param string $endpoint The endpoint of the expired subscription
     * @return mixed The result of the deletion operation
     */
    private function deleteExpiredSubscription($endpoint)
    {
        return Subscriber::where('endpoint', $endpoint)->first()?->delete();
    }

    /**
     * Sends notifications to subscribers, updates counters, and records the notification details in the database.
     *
     * @param int $productId The ID of the product for which notifications are sent
     * @param \Framework\Database\ORM\Collection $subscribers The collection of subscribers
     * @param array $payload The payload for the notification
     */
    private function send($productId, $subscribers, $payload)
    {
        if (filter_var(env('SEND_WEB_PUSH_NOTIFICATIONS', false), FILTER_VALIDATE_BOOLEAN) == false) return false;

        $notification_successes = 0;
        $notification_expired = 0;
        $notification_failed = 0;

        $this->queueNotifications($subscribers, $payload);
        $this->flushNotifications($notification_successes, $notification_expired, $notification_failed);

        Notification::create([
            'product_id' => $productId,
            'date' => Carbon::now(),
            'title' => $payload['title'],
            'content' => $payload['content'],
            'attachment' => $payload['image'],
            'url' => $payload['url'],
            'notifications_sent' => $notification_successes,
            'notifications_expired' => $notification_expired,
            'notifications_failed' => $notification_failed
        ]);
    }

    /**
     * Sends product availability notifications to subscribers.
     *
     * @param Product $product The product for which notifications are sent
     * @return bool False if no subscribers are found, otherwise true
     */
    public function sendProductNotification(Product $product)
    {
        $subscribers = $this->retrieveSubscribers($product);

        if ($subscribers->isEmpty()) return false;

        $payload = [
            'title' => "The watched product is available",
            'content' => "The product {$product->name} is available at a price of {$product->last_known_price} PLN",
            'image' => $product->image,
            'url' => $product->url,
            'actions' => [
                [
                    'name' => 'seeMore',
                    'title' => 'Go to the page'
                ]
            ]
        ];

        $this->send($product->id, $subscribers, $payload);
    }
}