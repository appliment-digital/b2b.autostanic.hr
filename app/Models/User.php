<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Exception;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function discountType()
    {
        return $this->belongsTo(DiscountType::class);
    }

    public static function add($data)
    {
        $user = new self();

        $user->name = $data['name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->bitrix_company_id = $data['bitrix_company_id'];
        $user->delivery_point = $data['delivery_point'] ?? null;

        if (!empty($data['payment_method'])) {
            $user->payment_method = $data['payment_method']['name'];
        }

        if (!empty($data['discount_type'])) {
            $user->discount_type_id = $data['discount_type']['id'];
        }

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']['id']);
        }

        $user->save();
        $user->refresh();

        return $user;
    }

    public static function updateUser($id, $data)
    {
        $user = self::find($id);

        $user->name = $data['name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->bitrix_company_id = $data['bitrix_company_id'];
        $user->delivery_point = $data['delivery_point'] ?? null;
        if (!empty($data['payment_method'])) {
            $user->payment_method = $data['payment_method']['name'];
        }
        if (!empty($data['discount_type'])) {
            $user->discount_type_id = $data['discount_type']['id'];
        }

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']['id']);
        }

        $user->save();
        $user->refresh();

        return $user;
    }

    public static function changeStatus($id, $active)
    {
        $user = self::find($id);

        $user->active = $active;

        $user->save();
        $user->refresh();

        return $user;
    }

    public static function getWithDiscoutTypeId($dicountTypeId)
    {
        return self::where('discount_type_id', $dicountTypeId)->get();
    }

    public static function removeDiscoutType($id)
    {
        $user = self::find($id);

        $user->discount_type_id = null;

        $user->save();
        $user->refresh();

        return $user;
    }
}
