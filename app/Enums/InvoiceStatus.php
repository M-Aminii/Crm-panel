<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self informal()
 * @method static self formal()
 * @method static self prebuy()
 */
final class InvoiceStatus extends Enum
{
    const InFormal = 'informal';
    const Formal = 'formal';
    const PreBuy = 'prebuy';

    protected static function values(): array
    {
        return [
            'informal' => self::InFormal,
            'formal' => self::Formal,
            'prebuy' => self::PreBuy,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::InFormal => 'informal',
            self::Formal => 'formal',
            self::PreBuy  => 'prebuy' ,
        ];
    }
}

