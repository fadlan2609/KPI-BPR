<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BPR extends Model
{
    use HasFactory;

    protected $table = 'bpr';

    protected $fillable = [
        'sandi_bpr',
        'nama_bpr',
        'jenis_bpr',
        'jenis_lembaga',
        'kategori',
        'no_telp',
        'alamat',
        'email',
        'website',
        'logo'
    ];
}