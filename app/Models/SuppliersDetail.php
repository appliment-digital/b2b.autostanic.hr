<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeliveryDeadline;
use App\Models\Warrant;

class SuppliersDetail extends Model
{
    use HasFactory;

    protected $fillable = ['description'];

    public function warrant()
    {
        return $this->belongsTo(Warrant::class);
    }

    public function deliveryDeadline()
    {
        return $this->belongsTo(DeliveryDeadline::class);
    }

    public static function add($data)
    {
        $suppliersDetail = new self();

        $suppliersDetail->web_db_supplier_id = $data['web_db_supplier_id'];
        $suppliersDetail->web_db_category_id = $data['web_db_category_id'];
        $suppliersDetail->web_db_product_id = $data['web_db_product_id'];
        $suppliersDetail->mark_up = $data['mark_up'];
        $suppliersDetail->expenses = $data['expenses'] ?? null;
        $suppliersDetail->warrant_id = $data['warrant_id'] ?? null;
        $suppliersDetail->delivery_deadline_id =
            $data['delivery_deadline_id'] ?? null;

        $suppliersDetail->save();
        $suppliersDetail->refresh();

        return $suppliersDetail;
    }

    public static function updateSuppliersDetail($id, $data)
    {
        $suppliersDetail = self::where('web_db_supplier_id', $id);

        $suppliersDetail->mark_up = $data['mark_up'];
        $suppliersDetail->expenses = $data['expenses'] ?? null;
        $suppliersDetail->warrant_id = $data['warrant_id'] ?? null;
        $suppliersDetail->delivery_deadline_id =
            $data['delivery_deadline_id'] ?? null;

        $suppliersDetail->save();
        $suppliersDetail->refresh();

        return $suppliersDetail;
    }
}
