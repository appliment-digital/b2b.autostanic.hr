<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discount'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public static function add($data)
    {
        $userIds = [1, 2, 3]; // Array of user IDs
        $discountType = new DiscountType();
        $discountType->name = 'Your Discount Type Name';
        $discountType->save();

        // Attach users to the discount type
        $discountType->users()->attach($userIds);
    }

    public static function updateDiscountType($id, $data)
    {
        $discountType = DiscountType::find($id);

        // Attach users to the discount type
        $users = [1, 2, 3]; // Array of user IDs
        $discountType->users()->attach($users);
    }

    public static function getAll()
    {
        return DiscountType::with('users')->get();
    }

    public static function get($id)
    {
        return DiscountType::with('users')->find($id);
    }
}
