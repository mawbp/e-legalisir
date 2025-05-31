<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    //
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
      'id',
      'judul',
      'isi',
      'expired_at',
      'created_at',
      'updated_at',
    ];
}
