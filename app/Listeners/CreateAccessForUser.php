<?php
// app/Listeners/CreateAccessForUser.php

namespace App\Listeners;

use App\Enums\AccessPayment;
use App\Events\UserCreated;
use App\Models\Access;

class CreateAccessForUser
{
    public function handle(UserCreated $event)
    {
        $user = $event->user;
        Access::create([
            'user_id' => $user->id,
            'max_discount' => 20,
            'payment_terms' => AccessPayment::CASH,
            'min_pre_payment' => 30,
        ]);
    }
}
