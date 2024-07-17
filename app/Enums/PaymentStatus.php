<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self paid()
 * @method static self unpaid()
 * @method static self suspend()
 * @method static self cancel()
 */
final class PaymentStatus extends Enum
{
    const Paid = 'paid';
    const Unpaid = 'unpaid';
    const Suspend = 'suspend';
    const Cancel = 'cancel';


    protected static function values(): array
    {
        return [
            'paid' => self::Paid,
            'unpaid' => self::Unpaid,
            'suspend' => self::Suspend,
            'cancel' => self::Cancel,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::Paid => 'paid',
            self::Unpaid => 'unpaid',
            self::Suspend => 'suspend',
            self::Cancel => 'cancel',
        ];
    }
}

