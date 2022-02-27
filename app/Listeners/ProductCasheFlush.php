<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductCasheFlush
{

    public function handle($event)
    {
        \Cache::forget('products');
    }
}
