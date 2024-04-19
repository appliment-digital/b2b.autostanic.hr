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
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function discountTypes()
    {
        return $this->belongsToMany(DiscountType::class);
    }

    public static function add($data)
    {
        $user = new self();

        $user->name = $data["name"];
        $user->last_name = $data["last_name"];
        $user->email = $data["email"];
        $user->bitrix_company_id = $data["bitrix_company_id"];
        $user->delivery_point = $data["delivery_point"];
        $user->payment_method = $data["payment_method"];

        if (strlen($data["password"]) >= 6) {
            $user->password = Hash::make($data["password"]);
        } else {
            return "Lozinka treba imati bar 6 znakova.";
        }

        if (!empty($data['roles'])) {
            $user->syncRoles($data["roles"]["id"]);
        }

        $user->save();
        $user->refresh();

        if (!empty($data['discount_types'])) {
            $discountTypeIds = array_column($data['discount_types'], 'id');
            $user->discountTypes()->attach($discountTypeIds);
        }

        return $user;
    }

    public static function updateUser($id, $data)
    {
        $user = self::find($id);

        $user->name = $data["name"];
        $user->last_name = $data["last_name"];
        $user->email = $data["email"];
        $user->bitrix_company_id = $data["bitrix_company_id"];
        $user->delivery_point = $data["delivery_point"];
        $user->payment_method = $data["payment_method"];

        if (isset($data["password"]) && strlen($data["password"]) >= 6) {
            $user->password = Hash::make($data["password"]);
        } elseif (!isset($data["password"]) || strlen($data["password"]) == 0) {
            // Keep the old password
        } else {
            return "Lozinka treba imati bar 6 znakova.";
        }

        if (!empty($data['roles'])) {
            $user->syncRoles($data["roles"]["id"]);
        }

        $user->save();
        $user->refresh();

        if (!empty($data['discount_types'])) {
            $user->discountTypes()->detach();

            $discountTypeIds = array_column($data['discount_types'], 'id');
            $user->discountTypes()->attach($discountTypeIds);
        }

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
}
