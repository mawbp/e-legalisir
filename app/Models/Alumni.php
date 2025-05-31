<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
  public $timestamps = false;
  protected $table = 'alumni';

  //
  use HasFactory;
  protected $fillable = [
    'user_id',
    'nim',
    'nama',
    'prodi',
    'tahun_angkatan',
    'kode_file',
  ];

  public function user(){
    return $this->belongsTo(User::class);
  }
}
