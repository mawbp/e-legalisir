<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    //
    use HasFactory;

    public $timestamps = false;
    protected $table = 'alamat';

    protected $fillable = [
      'user_id',
      'kelurahan',
      'kecamatan',
      'kabupaten',
      'provinsi',
      'kode_pos',
      'catatan',
      'label',
    ];

    public function user(){
      return $this->belongsTo(User::class);
    }

}
