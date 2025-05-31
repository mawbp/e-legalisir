<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    //
  protected $table = 'prodi';

    use HasFactory;
    protected $fillable = [
      'nama_prodi',
    ];
}
