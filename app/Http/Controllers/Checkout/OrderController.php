<?php

namespace App\Http\Controllers\Checkout;

use App\Events\OrderCompletedEvent;
use App\Link;
use App\Order;
use App\Product;
use App\OrderItem;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class OrderController
{
    public function store(Request $request)
    {
        $link = Link::whereCode($request->input('code'))->first();

        \DB::beginTransaction();

        $order = new Order();
        $order->first_name = $request->input('first_name');
        $order->last_name = $request->input('last_name');
        $order->email = $request->input('email');
        $order->code = $link->code;
        $order->user_id = $link->user->id;
        $order->influencer_email = $link->user->email;
        $order->address = $request->input('address');
        $order->address2 = $request->input('address2');
        $order->city = $request->input('city');
        $order->country = $request->input('country');
        $order->zip = $request->input('city');

        $order->save();

        $lineItems = [];

        foreach ($request->input('items') as $item) {
            $product = Product::find($item['product_id']);

            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_title = $product->title;
            $orderItem->price = $product->price;
            $orderItem->quantity = $item['quantity'];

            $orderItem->influencer_revenue = 0.1 * $product->price * $item['quantity'];
            $orderItem->admin_revenue = 0.9 * $product->price * $item['quantity'];

            $orderItem->save();

            $lineItems[] = [
                'name' => $product->title,
                'description' => $product->description,
                'images' => [
                    $product->image
                ],
                'amount' => 100 * $product->price,
                'currency' => 'usd',
                'quantity' => $orderItem->quantity

            ];
        }

        $stripe = Stripe::make(env('STRIPE_SECRET'));

        $source = $stripe->checkout()->sessions()->create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'success_url' => env('CHECKOUT_URL') . '/success?source={CHECHOUT_SESSION_ID}',
            'cancel_url' => env('CHECKOUT_URL') . '/error'
        ]);

        $order->transaction_id = $source['id'];
        $order->save();

        \DB::commit();

        return $source;
    }

    public function confirm(Request $request)
    {
        $order = Order::whereTransactionId($request->input('source'))->first();

        if (!$order) {
            return response([
                'error' => 'order not found'
            ], 404);
        }
        $order->complete = 1;
        $order->save();

        event(new OrderCompletedEvent($order));

        // \Mail::send('admin', ['order' => $order], function (Message $message) {
        //     $message->to('aurora10@gmail.com');
        //     $message->from('aurora10@gmail.com');
        //     $message->subject('A new order has been completed');
        // });

        // \Mail::send('influencer', ['order' => $order], function (Message $message) use ($order) {
        //     $message->to($order->influencer_email);
        //     $message->from('aurora10@gmail.com');
        //     $message->subject('A new order has been completed');
        // });

        return response([
            'message' => 'success'
        ]);
    }
}
