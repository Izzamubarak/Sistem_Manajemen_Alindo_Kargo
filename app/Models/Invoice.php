<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'package_id',
        'invoice_number',
        'status',
        'total_amount',
        'created_by'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

  public function package()
{
    return $this->belongsTo(Data_paket::class, 'package_id');
}


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
