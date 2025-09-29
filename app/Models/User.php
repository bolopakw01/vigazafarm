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

    protected $table = 'pengguna';

    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diperbarui_pada';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'nama_pengguna',
        'surel',
        'kata_sandi',
        'peran',
        'foto_profil',
    ];

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'nama_pengguna';
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
