<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    // Wajib disesuaikan dengan tabel kamu
    protected $table = 'absensi'; 
    protected $primaryKey = 'id_absensi';
    
    // Matikan timestamps kalau di tabel tidak ada created_at/updated_at
    public $timestamps = false; 
    
    protected $fillable = ['id_siswa', 'tanggal', 'status'];
}