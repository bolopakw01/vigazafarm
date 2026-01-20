<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'vf_pengguna';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'nama_pengguna',
        'surel',
        'nomor_telepon',
        'kata_sandi',
        'peran',
        'foto_profil',
        'alamat',
    ];

    /**
     * Get the name of the unique identifier for the user.
     * 
     * NOTE: This should return the primary key column name (id), NOT username.
     * Returning 'nama_pengguna' causes Auth::id() to return username string
     * instead of integer ID, breaking foreign key relationships.
     * 
     * If you want to login with username, use getAuthIdentifier() instead,
     * or configure in config/auth.php
     */
    public function getAuthIdentifierName()
    {
        return 'id';  // Changed from 'nama_pengguna' to 'id'
    }
    
    /**
     * Get the unique identifier for the user (username for login).
     * This is used during authentication to find the user.
     */
    public function getAuthIdentifier()
    {
        // Return actual ID (integer) for Auth::id()
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'kata_sandi',
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
            'surel_terverifikasi_pada' => 'datetime',
            'kata_sandi' => 'hashed',
        ];
    }

    // Accessor untuk email
    public function getEmailAttribute()
    {
        return $this->surel;
    }

    // Mutator untuk email
    public function setEmailAttribute($value)
    {
        $this->surel = $value;
    }

    // Accessor untuk password
    public function getPasswordAttribute()
    {
        return $this->kata_sandi;
    }

    // Mutator untuk password
    public function setPasswordAttribute($value)
    {
        $this->kata_sandi = $value;
    }

    // Accessor untuk username
    public function getUsernameAttribute()
    {
        return $this->nama_pengguna;
    }

    // Mutator untuk username
    public function setUsernameAttribute($value)
    {
        $this->nama_pengguna = $value;
    }

    // Accessor untuk profile_picture
    public function getProfilePictureAttribute()
    {
        return $this->foto_profil;
    }

    // Mutator untuk profile_picture
    public function setProfilePictureAttribute($value)
    {
        $this->foto_profil = $value;
    }

    // Accessor untuk email_verified_at
    public function getEmailVerifiedAtAttribute()
    {
        return $this->surel_terverifikasi_pada;
    }

    // Mutator untuk email_verified_at
    public function setEmailVerifiedAtAttribute($value)
    {
        $this->surel_terverifikasi_pada = $value;
    }
}
