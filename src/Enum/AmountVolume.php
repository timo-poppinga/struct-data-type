<?php

declare(strict_types=1);

namespace Struct\DataType\Enum;

enum AmountVolume: string
{
    case Base = '';
    case Thousand = 'T';
    case Million = 'M';
}
