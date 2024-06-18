<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self location()
 * @method static self factory()
 */
final class InvoiceDelivery extends Enum
{

    const Delivery_Location = 'location';

    const Delivery_Factory = 'factory';

    protected static function values(): array
    {
        return [
            'location' => self::Delivery_Location,
            'factory' => self::Delivery_Factory,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::Delivery_Location => 'location',
            self::Delivery_Factory => 'factory',
        ];
    }
}


