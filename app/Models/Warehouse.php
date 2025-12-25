<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'city',
        'address',
        'capacity',
        // 'manager_id',
        // 'status',
        'description',
    ];

    // public function manager()
    // {
    //     return $this->belongsTo(User::class, 'manager_id');
    // }

    // Relationship: Products stored in this warehouse
    public function stocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }
}
