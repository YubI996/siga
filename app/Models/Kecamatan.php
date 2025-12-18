<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Kecamatan extends Model
{
    /** @use HasFactory<\Database\Factories\KecamatanFactory> */
    use HasFactory;
    use HasUuids;
    
    protected $fillable = [
        'kode',
        'nama',
    ];

    public function kelurahans()
    {
        return $this->hasMany(Kelurahan::class);
    }
}
