<?php 

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static reviced()
 * @method static static ongoing()
 */
final class OrderStatus extends Enum
{
    const RECIVED = 1;
    const ONGOING = 2;
}
