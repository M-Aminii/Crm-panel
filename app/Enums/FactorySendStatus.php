<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self yes()
 * @method static self no()
 */
final class FactorySendStatus extends Enum
{
    const YES = 'yes';
    const NO = 'no';

    protected static function values(): array
    {
        return [
            'yes' => self::YES,
            'no' => self::NO,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::YES => 'yes',
            self::NO => 'no',
        ];
    }
}


