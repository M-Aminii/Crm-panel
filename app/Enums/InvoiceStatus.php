<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self informal()
 * @method static self formal()
 */
final class InvoiceStatus extends Enum
{
    const InFormal = 'informal';
    const Formal = 'formal';

    protected static function values(): array
    {
        return [
            'informal' => self::InFormal,
            'formal' => self::Formal,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::InFormal => 'informal',
            self::Formal => 'formal',
        ];
    }
}


