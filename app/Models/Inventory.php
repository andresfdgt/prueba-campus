<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventarios';

    protected $fillable = [
        'id_bodega', 'id_producto', 'cantidad', 'created_by', 'updated_by'
    ];

    public function producto()
    {
        return $this->belongsTo(Product::class, 'id_producto');
    }

    public function bodega()
    {
        return $this->belongsTo(Warehouse::class, 'id_bodega');
    }
}
