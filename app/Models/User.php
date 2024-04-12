<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    public static function add($data)
    {
        $user = new self();

        $user->name = $data["name"];
        $user->last_name = $data["last_name"];
        $user->email = $data["email"];
        $user->bitrix_company_id = $data["bitrix_company_id"];
        
        $user->save();
        $user->refresh();

        return $user;
    }

    public static function updateUser($id, $data)
    {
        $user = self::find($id);

        $user->name = $data["name"];
        $user->last_name = $data["last_name"];
        $user->email = $data["email"];
        $user->bitrix_company_id = $data["bitrix_company_id"];
        
        $user->save();
        $user->refresh();

        return $user;
    }

    public static function deactivate($id)
    {
        $user = self::find($id);

        $user->active = 0;
        
        $user->save();
        $user->refresh();

        return $user;
    }

}
