<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SuppliersDetail;

class Warrent extends Model
{
    use HasFactory;

    protected $fillable = ['description'];

    public function suppliersDetails()
    {
        return $this->hasMany(SuppliersDetail::class);
    }
}
