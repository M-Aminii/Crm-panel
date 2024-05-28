<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self active()
 * @method static self inactive()
 * @method static self incomplete()
 */
final class CustomerStatus extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const INCOMPLETE = 'incomplete';

    protected static function values(): array
    {
        return [
            'active' => self::ACTIVE,
            'inactive' => self::INACTIVE,
            'incomplete' => self::INCOMPLETE
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

