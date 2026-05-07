<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom foto ke tabel siswa jika belum ada
        if (!Schema::hasColumn('siswa', 'foto')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->string('foto')->nullable()->after('no_telp_ortu');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('siswa', 'foto')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->dropColumn('foto');
            });
        }
    }
};
