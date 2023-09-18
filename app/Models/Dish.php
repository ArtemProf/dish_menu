<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Dish
 *
 * @property int                    $id
 * @property int                    $dish_category_id
 * @property   string               $title
 * @property   string               $video
 * @property   string               $tag
 * @property   string               $image
 * @property   string               $description
 * @property   string               $nutritional_value
 * @property   string               $exclaim
 * @property-read  DishIngredient[] $ingredients
 * @property-read  DishCategory     $category
 */
class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'dish_category_id',
        'title',
        'video',
        'tag',
        'image',
        'description',
        'nutritional_value',
        'exclaim',
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(DishIngredient::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DishCategory::class);
    }
}
