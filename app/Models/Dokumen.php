<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    //
  public $timestamps = false;
  protected $table = 'dokumen';

    use HasFactory;
    protected $fillable = [
      'nama_dokumen',
      'deskripsi',
      'pemilik',
      'harga_per_lembar'
    ];

    public function permohonan(){
      return $this->hasMany(Permohonan::class);
    }
}
