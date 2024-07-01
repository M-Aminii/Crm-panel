<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self Active()
 * @method static self Inactive()
 * @method static self Incomplete()
 */
final class CustomerStatus extends Enum
{
    const ACTIVE = 'Active';
    const INACTIVE = 'Inactive';
    const INCOMPLETE = 'Incomplete';

    protected static function values(): array
    {
        return [
            'Active' => self::ACTIVE,
            'Inactive' => self::INACTIVE,
            'Incomplete' => self::INCOMPLETE
        ];
    }

    protected static function labels(): array
    {
        return [
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::INCOMPLETE => 'Incomplete'
        ];
    }
}

