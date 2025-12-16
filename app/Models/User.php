<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable, HasRoles, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        // tambahan sesuai desain
        'level_wilayah',  // rt, lurah, camat, super-admin, operator, viewer
        'rt_id',
        'kelurahan_id',
        'kecamatan_id',
        'opd_id',
        'is_active',
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
            'is_active'         => 'boolean',
        ];
    }
     /*
    |--------------------------------------------------------------------------
    | Relasi Wilayah
    |--------------------------------------------------------------------------
    */

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Level Wilayah
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        // Bisa pakai role Spatie atau level_wilayah, atau dua-duanya
        return $this->hasRole('super-admin') || $this->level_wilayah === 'super-admin';
    }

    public function isCamat(): bool
    {
        return $this->level_wilayah === 'camat';
    }

    public function isLurah(): bool
    {
        return $this->level_wilayah === 'lurah';
    }

    public function isRt(): bool
    {
        return $this->level_wilayah === 'rt';
    }

    /*
    |--------------------------------------------------------------------------
    | Scope helper
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk hanya user yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
