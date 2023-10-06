<?php

declare(strict_types=1);

namespace Struct\DataType\Enum;

enum RateType: string
{
    case Percent = '%';
    case Permille = '‰';
}
