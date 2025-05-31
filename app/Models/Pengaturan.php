<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
  public $timestamps = false;
  protected $table = 'pengaturan';

  //
  use HasFactory;
  protected $fillable = [
    'nama',
    'nilai',
  ];
}