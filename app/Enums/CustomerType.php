<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self legal()
 * @method static self individual()
 */
final class CustomerType extends Enum
{
    const Type_Legal = 'legal';
    const Type_Individual = 'individual';

    protected static function values(): array
    {
        return [
            'legal' => self::Type_Legal,
            'individual' => self::Type_Individual,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::Type_Legal => 'legal',
            self::Type_Individual => 'individual',
        ];
    }
}


