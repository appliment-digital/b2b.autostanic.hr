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
        $discountType = new self();
        $discountType->name = $data['name'];
        $discountType->discount = $data['discount'];

        $discountType->save();

        $userIds = array_column($data['users'], 'id');
        $discountType->users()->attach($userIds);

        return $discountType;
    }

    public static function updateDiscountType($id, $data)
    {
        $discountType = self::find($id);

        $discountType->name = $data['name'];
        $discountType->discount = $data['discount'];

        $discountType->save();

        // Detach all existing users
        $discountType->users()->detach();

        $userIds = array_column($data['users'], 'id');
        $discountType->users()->attach($userIds);

        return $discountType;
    }

    public static function getAll()
    {
        return self::with('users')->get();
    }

    public static function get($id)
    {
        return DiscountType::with('users')->find($id);
    }

    public static function erase($id)
    {
        $discountType = self::find($id);

        if ($discountType) {
            return $discountType->delete();
        }
    }
}
