<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int                 $id
 * @property int                 $user_id
 * @property string              $title
 * @property boolean             $is_default
 * @property int                 $created_at
 * @property int                 $updated_at
 * @property-read User           $user
 * @property-read CookListItem[] $items
 *
 */
class CookList extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'is_default',
    ];

    protected $with = ['items'];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    public static function findOrCreateDefault(int $userId)
    {
        $cookList = CookList::whereUserId($userId)->whereIsDefault(true)->first();
        if (!$cookList) {
            $cookList             = new CookList();
            $cookList->user_id    = $userId;
            $cookList->title      = 'default';
            $cookList->is_default = true;
            $cookList->save();
        }

        return $cookList;
    }

    public function items(): HasMany
    {
        return $this->hasMany(CookListItem::class);
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
