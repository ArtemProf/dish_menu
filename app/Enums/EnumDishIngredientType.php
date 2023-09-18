<?php

namespace App\Enums;

enum EnumDishIngredientType: string
{
    case PRODUCT = 'product';
    case DISH    = 'dish';

    public function getName(): string
    {
        return match ($this) {
            self::PRODUCT => 'Продукт',
            self::DISH    => 'Блюдо',
        };
    }

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
