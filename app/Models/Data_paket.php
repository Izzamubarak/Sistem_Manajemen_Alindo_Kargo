<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data_paket extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'resi',
        'description',
        'kota_asal',
        'kota_tujuan',
        'no_hp_pengirim',
        'penerima',
        'no_hp_penerima',
        'alamat_penerima',
        'weight',
        'jumlah_koli',
        'volume',
        'cost',
        'created_by',
        'status',
        'alasan_gagal'  
    ];



    // public function vendor()
    // {
    //     return $this->belongsTo(Vendor::class);
    // }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'package_id');
    }
    public function vendors()
    {
        return $this->belongsToMany(\App\Models\Vendor::class, 'data_paket_vendor')
            ->withPivot('biaya_vendor')
            ->withTimestamps();
    }
    public function Biaya_operasional()
    {
        return $this->hasOne(Biaya_operasional::class, 'resi', 'resi');
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($paket) {
            $paket->Biaya_operasional()?->delete();
        });
    }
}
