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

    public function warrent()
    {
        return $this->belongsTo(Warrent::class);
    }

    public function deliveryDeadline()
    {
        return $this->belongsTo(DeliveryDeadline::class);
    }

    public static function add($data)
    {
        $suppliersDetails = [];

        foreach ($data['categoriesIds'] as $categoryId) {
            $suppliersDetail = new self();
            $suppliersDetail->web_db_supplier_id = $data['supplierId'];
            $suppliersDetail->web_db_category_id = $categoryId;
            $suppliersDetail->mark_up = $data['supplierDetail']['markUp'];
            $suppliersDetail->warrent_id =
                $data['supplierDetail']['warrent']['id'] ?? null;
            $suppliersDetail->delivery_deadline_id =
                $data['supplierDetail']['deliveryDeadline']['id'] ?? null;
            $suppliersDetail->min_product_cost =
                $data['minProductCost'] ?? null;
            $suppliersDetail->max_product_cost =
                $data['maxProductCost'] ?? null;
            $suppliersDetail->expenses =
                $data['supplierDetail']['expenses'] ?? null;

            $suppliersDetail->save();
            $suppliersDetail->refresh();

            $suppliersDetails[] = $suppliersDetail;
        }

        return $suppliersDetails;
    }

    public static function updateSuppliersDetail($id, $data)
    {
        $suppliersDetail = self::find($id);

        $suppliersDetail->mark_up = $data['markUp'];
        $suppliersDetail->expenses = $data['expenses'] ?? null;
        $suppliersDetail->warrent_id = $data['warrent']['id'] ?? null;
        $suppliersDetail->delivery_deadline_id =
            $data['deliveryDeadline']['id'] ?? null;

        $suppliersDetail->save();
        $suppliersDetail->refresh();

        return $suppliersDetail;
    }
}
