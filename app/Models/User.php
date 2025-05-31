<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'password',
        'email',
        'phone',
        'alamat_id',
        'alumni_id',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isProfileComplete()
    {
      $requiredFields = ['name', 'email', 'phone', 'alamat_id'];
      foreach ($requiredFields as $field) {
        if(!isset($this->$field) || $this->$field === '' || $this->$field === null){
          return false;
        }
      }
      return true;
    }

    public function permohonan(){
      return $this->hasMany(Permohonan::class);
    }

    public function logStatus(){
      return $this->hasMany(LogStatus::class);
    }

    public function alamat(){
      return $this->hasMany(Alamat::class);
    }

    public function alumni(){
      return $this->hasOne(Alumni::class);
    }
}
