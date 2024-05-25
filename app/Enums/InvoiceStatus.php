<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self informal()
 * @method static self formal()
 */
final class InvoiceStatus extends Enum
{
    const Status_InFormal = 'informal';
    const Status_Formal = 'formal';

    protected static function values(): array
    {
        return [
            'informal' => self::Status_InFormal,
            'formal' => self::Status_Formal,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::Status_InFormal => 'informal',
            self::Status_Formal => 'formal',
        ];
    }
}


