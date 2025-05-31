<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    //
    use HasFactory;
    protected $table = 'pembayaran';


    protected $fillable = [
      'permohonan_id',
      'order_id',
      'metode_pembayaran',
      'metode_pengiriman',
      'biaya_kurir',
      'jumlah_bayar',
      'alamat_id',
      'status_pembayaran',
      'bukti_pembayaran',
      'expired_at',
      'snap_token',
    ];

    public function permohonan(){
      return $this->hasMany(Permohonan::class);
    }
}
