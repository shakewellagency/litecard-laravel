<?php

namespace Shakewell\Litecard\Enums;

enum CardStatusEnum: string
{
    case INACTIVE = 'INACTIVE';

    case ACTIVE = 'ACTIVE';

    case DELETED = 'DELETED';

    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }
}
