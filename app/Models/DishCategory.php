<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * class DishCategory
 *
 * @property int         $id
 * @property int         $parent_id
 * @property string      $title
 * @property int         $sort
 * @property-read Dish[] $dishes
 */
class DishCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'parent_id',
        'title',
        'sort'
    ];

    public $timestamps = false;

    public function dishes(): HasMany
    {
        return $this->hasMany(Dish::class);
    }
}
