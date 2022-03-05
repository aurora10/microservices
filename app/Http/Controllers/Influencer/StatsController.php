<?php

namespace App\Http\Controllers\Influencer;

use App\Http\Controllers\Controller;
use App\Link;
use App\Order;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class StatsController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $links = Link::where('user_id', $user->id)->get();

        return $links->map(function(Link $link){
            $orders = Order::where('code', $link->code)->where('complete', 1)->get();

            return [
                'code' => $link->code,
                'count' => $orders->count(),
                'revenue' => $orders->sum(function(Order $order){
                    return $order->influencer_total;
                }),
            ];
        });

    }

    public function ranking() {
        return Redis::zrevrange('rankings', 1, -1 , 'WITHSCORES');//\Cache::get('rankings');//
    }
}
