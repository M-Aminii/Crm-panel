<?php

namespace App\Enums;

use Spatie\Enum\Laravel\Enum;

/**
 * @method static self pending_approval()
 * @method static self rejected()
 */
final class InformalInvoiceStatus extends Enum
{
    const PENDING_APPROVAL   = 'pending_approval';
    const REJECTED  = 'rejected';

    protected static function values(): array
    {
        return [
            'rejected' => self::REJECTED,
            'pending_approval' => self::PENDING_APPROVAL,
        ];
    }

    protected static function labels(): array
    {
        return [
            self::REJECTED => 'rejected',
            self::PENDING_APPROVAL => 'pending_approval',
        ];
    }
}

