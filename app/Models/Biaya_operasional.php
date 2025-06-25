<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Biaya_operasional extends Model
{
    use HasFactory;

    protected $fillable = [
        'resi',
        'total_vendor',
        'total_paket',
        'biaya_lainnya',
        'total_keseluruhan',
        'created_by',
    ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function paket()
    {
        return $this->belongsTo(\App\Models\Data_paket::class, 'resi', 'resi');
    }
}
