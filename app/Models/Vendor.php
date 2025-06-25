<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'address', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function packages()
    {
        return $this->hasMany(Data_paket::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public function data_pakets()
    {
        return $this->belongsToMany(Data_paket::class, 'data_paket_vendor')->withPivot('biaya_vendor');
    }
}
