<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    //
    use HasFactory;
    protected $table = 'permohonan';


    protected $fillable = [
      'user_id',
      'permohonan_id',
      'jumlah_cetak',
      'harga_per_lembar',
      'dokumen_id',
      'status_permohonan',
      'pembayaran_id',
      'kurir',
      'tipe_kurir',
      'order_id',
      'tracking_id',
      'metode_pengambilan',
    ];

    

    public function user(){
      return $this->belongsTo(User::class);
    }

    public function pembayaran(){
      return $this->belongsTo(Pembayaran::class);
    }
    
    public function dokumen(){
      return $this->belongsTo(Dokumen::class);
    }
}
