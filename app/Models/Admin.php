<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Contracts\Auth\Authenticatable;

class Admin extends Authenticatable
{
    //
    use HasFactory, Notifiable;

    protected $fillable = [
      'username',
      'password',
    ];

    protected $hidden = [
      'password',
      'remember_token',
    ];
}
