<?php

namespace App;

use DB;
use App\Exceptions\InsufficientBalance;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|self newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|self newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|self query()
 * @method static \Illuminate\Database\Eloquent\Builder|self whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static self create(array $attributes = [])
 * @method static self findOrFail(mixed $id, array $columns = ['*'])
 * @method static self|null find(mixed $id, array $columns = ['*'])
 * @property string $currency
 * @method static \Illuminate\Database\Eloquent\Builder|self whereCurrency($value)
 * @property float $balance
 * @method static \Illuminate\Database\Eloquent\Builder|self whereBalance($value)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'currency', 'balance', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    #region Relationships

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    #endregion

    #region Methods

    public function deposit(float $amount): Transaction
    {
        try {
            DB::beginTransaction();

            $transaction = $this->transactions()->create([
                'type'   => 'deposit',
                'amount' => $amount,
            ]);

            $this->increment('balance', $amount);

            DB::commit();

            return $transaction;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function withdraw(float $amount): Transaction
    {
        $balance = $this->balance;

        if ($amount > $balance) {
            throw new InsufficientBalance($amount, $balance, $this->currency);
        }

        try {
            DB::beginTransaction();

            $transaction = $this->transactions()->create([
                'type'   => 'withdraw',
                'amount' => $amount,
            ]);

            $this->decrement('balance', $amount);

            DB::commit();

            return $transaction;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    #endregion
}
