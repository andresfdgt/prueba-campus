<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'created_by',
        'updated_by'
    ];

    public function inventarios()
    {
        return $this->hasMany(Inventory::class, 'id_producto', 'id');
    }

    public function getTotalAttribute()
    {
        return $this->inventarios()->sum('cantidad');
    }

    public function scopeOrderByTotal($query, $direction = 'desc')
    {
        return $query->withSum('inventarios as total', 'cantidad')
            ->orderBy('total', $direction);
    }
}
