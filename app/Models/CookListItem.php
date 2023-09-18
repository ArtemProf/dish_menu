<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int           $id
 * @property int           $cook_list_id
 * @property int           $dish_id
 * @property int           $user_id
 * @property int           $amount
 * @property-read Dish     $dish
 * @property-read CookList $cookList
 * @property-read User     $user
 */
class CookListItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'cook_list_id',
        'dish_id',
        'amount',
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    public function dish(): HasOne
    {
        return $this->hasOne(Dish::class, 'id', 'dish_id');
    }

    public function cookList(): BelongsTo
    {
        return $this->belongsTo(CookList::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateUser(int $userId)
    {
        $this->user_id = $userId;
        $this->save();
    }
}
