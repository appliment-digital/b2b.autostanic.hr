<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DiscountType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'discount'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function add($data)
    {
        $discountType = new self();
        $discountType->name = $data['name'];
        $discountType->discount = $data['discount'];

        $discountType->save();

        if (isset($data['users']) && is_array($data['users'])) {
            $userIds = array_column($data['users'], 'id');
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->discount_type_id = $discountType->id;
                    $user->save();
                }
            }
        }

        return $discountType;
    }

    public static function updateDiscountType($id, $data)
    {
        $discountType = self::find($id);

        $discountType->name = $data['name'];
        $discountType->discount = $data['discount'];

        $discountType->save();

        $usersWithDiscount = User::getWithDiscoutTypeId($id);

        $usersWithDiscountIds = $usersWithDiscount->pluck('id')->toArray();

        $userIds = array_column($data['users'], 'id');

        $usersForDeletingDiscoutTypeIds = array_diff(
            $usersWithDiscountIds,
            $userIds
        );

        foreach ($usersForDeletingDiscoutTypeIds as $id) {
            User::removeDiscoutType($id);
        }

        if (isset($data['users']) && is_array($data['users'])) {
            $userIds = array_column($data['users'], 'id');
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->discount_type_id = $discountType->id;
                    $user->save();
                }
            }
        }

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
