<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self cash()
 * @method static self cheque()
 */
final class AccessPayment extends Enum
{
    const CASH = 'cash';
    const CHEQUE = 'cheque';

    protected static function values(): array
    {
        return [
            'cash' => self::CASH,
            'cheque' => self::CHEQUE,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::CASH => 'cash',
            self::CHEQUE => 'cheque',
        ];
    }
}

