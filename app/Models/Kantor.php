<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kantor extends Model
{
    use HasFactory;

    protected $table = 'kantor';

    protected $fillable = [
        'nama',
        'alamat',
        'keterangan'
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class);
    }
}