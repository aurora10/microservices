<?php

namespace App;

use App\Role;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property int $role_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @mixin \Eloquent
 * @property int $is_influencer
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsInfluencer($value)
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];


    protected $hidden = [
        'password',
    ];
    public $timestamps = false;

    public function role() {
        return $this->hasOneThrough(Role::class, UserRole::class, 'user_id', 'id', 'id', 'role_id');
    }

    public function permissions() {
        return $this->role->permissions->pluck('name');
    }

    public function hasAccess($access) {
        return $this->permissions()->contains($access);
    }

    public function isAdmin() : bool {
        return $this->is_influencer === 0;
    }

    public function isInfluencer() : bool {
        return $this->is_influencer === 1;
    }

    public function getRevenueAttribute() {

        $orders = Order::where('user_id', $this->id)->where('complete', 1)->get();

        return $orders->sum(function (Order $order){
            return $order->influencer_total;
        });

    }

    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->last_name;
    }

}
