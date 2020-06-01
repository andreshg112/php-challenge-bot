<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Transaction
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|self newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|self newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|self query()
 * @method static \Illuminate\Database\Eloquent\Builder|self whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereUserId($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    protected $fillable = ['user_id', 'type', 'amount'];
}
