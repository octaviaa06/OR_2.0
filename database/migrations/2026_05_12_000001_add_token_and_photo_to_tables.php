<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom token ke tabel akun
        if (!Schema::hasColumn('akun', 'token')) {
            Schema::table('akun', function (Blueprint $table) {
                $table->string('token', 500)->nullable()->after('fcm_token');
            });
        }

        // Tambah kolom token_expired_at ke tabel akun
        if (!Schema::hasColumn('akun', 'token_expired_at')) {
            Schema::table('akun', function (Blueprint $table) {
                $table->timestamp('token_expired_at')->nullable()->after('token');
            });
        }

        // Tambah kolom foto ke tabel siswa jika belum ada
        if (!Schema::hasColumn('siswa', 'foto')) {
            Schema::table('siswa', function (Blueprint $table) {
                $table->string('foto', 500)->nullable()->after('no_telp_ortu');
            });
        }
    }

    public function down(): void
    {
        Schema::table('akun', function (Blueprint $table) {
            $table->dropColumn(['token', 'token_expired_at']);
        });
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
