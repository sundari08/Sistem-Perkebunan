<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class hasilpanen extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'tgl',
        'estate',
        'divisi',
        'blok',
        'mandor',
        'kerani',
        'tph',
        'pemanen',
        'janjang',
        'matang',
        'mentah',
        'kurangmatang',
        'lewatmatang',
        'partenorcarpi',
        'buahbatu'
    ];
}
