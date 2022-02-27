<?php

namespace App\Listeners;

use App\Events\AdminAddedEvent;
use Illuminate\Mail\Message;
//use App\Listeners\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAdminAddedListener
{

    public function handle( AdminAddedEvent $event)
    {
        $user = $event->user;

        \Mail::send('admin.adminAdded', [], function (Message $message) use ($user) {
            $message->to($user->email);
            $message->from('aurora10@gmail.com');
            $message->subject('You have been added to the admin a');
        });
    }
}
