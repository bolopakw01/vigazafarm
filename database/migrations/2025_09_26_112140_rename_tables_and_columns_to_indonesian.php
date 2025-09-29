<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename tables
        Schema::rename('users', 'pengguna');
        Schema::rename('hatchings', 'penetasan');
        Schema::rename('growings', 'pembesaran');
        Schema::rename('productions', 'produksi');
        Schema::rename('feeds', 'pakan');
        Schema::rename('eggs', 'telur');
        Schema::rename('deaths', 'kematian');

        // Rename columns for pengguna
        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->renameColumn('password', 'kata_sandi');
            $table->enum('peran', ['owner', 'operator'])->default('operator')->after('kata_sandi');
            $table->renameColumn('name', 'nama');
            $table->renameColumn('username', 'nama_pengguna');
            $table->renameColumn('email', 'surel');
            $table->renameColumn('email_verified_at', 'surel_terverifikasi_pada');
            $table->renameColumn('remember_token', 'token_ingat');
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });

        // Rename columns for penetasan
        Schema::table('penetasan', function (Blueprint $table) {
            $table->renameColumn('egg_storage_date', 'tanggal_simpan_telur');
            $table->renameColumn('egg_count', 'jumlah_telur');
            $table->renameColumn('hatching_date', 'tanggal_menetas');
            $table->renameColumn('hatched_count', 'jumlah_menetas');
            $table->renameColumn('doc_count', 'jumlah_doc');
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });

        // Rename columns for pembesaran
        Schema::table('pembesaran', function (Blueprint $table) {
            $table->renameColumn('entry_date', 'tanggal_masuk');
            $table->renameColumn('chick_count', 'jumlah_anak_ayam');
            $table->renameColumn('gender', 'jenis_kelamin');
            $table->renameColumn('ready_date', 'tanggal_siap');
            $table->renameColumn('ready_count', 'jumlah_siap');
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });

        // Rename columns for produksi
        Schema::table('produksi', function (Blueprint $table) {
            $table->renameColumn('start_date', 'tanggal_mulai');
            $table->renameColumn('hen_count', 'jumlah_indukan');
            $table->renameColumn('end_date', 'tanggal_akhir');
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });

        // Rename columns for pakan
        Schema::table('pakan', function (Blueprint $table) {
            $table->renameColumn('production_id', 'produksi_id');
            $table->renameColumn('date', 'tanggal');
            $table->renameColumn('amount_kg', 'jumlah_kg');
            $table->renameColumn('amount_sacks', 'jumlah_karung');
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });

        // Rename columns for telur
        Schema::table('telur', function (Blueprint $table) {
            $table->renameColumn('production_id', 'produksi_id');
            $table->renameColumn('date', 'tanggal');
            $table->renameColumn('count', 'jumlah');
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });

        // Rename columns for kematian
        Schema::table('kematian', function (Blueprint $table) {
            $table->renameColumn('production_id', 'produksi_id');
            $table->renameColumn('date', 'tanggal');
            $table->renameColumn('count', 'jumlah');
            $table->renameColumn('created_at', 'dibuat_pada');
            $table->renameColumn('updated_at', 'diperbarui_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse rename columns
        Schema::table('kematian', function (Blueprint $table) {
            $table->renameColumn('dibuat_pada', 'created_at');
            $table->renameColumn('diperbarui_pada', 'updated_at');
            $table->renameColumn('jumlah', 'count');
            $table->renameColumn('tanggal', 'date');
            $table->renameColumn('produksi_id', 'production_id');
        });

        Schema::table('telur', function (Blueprint $table) {
            $table->renameColumn('dibuat_pada', 'created_at');
            $table->renameColumn('diperbarui_pada', 'updated_at');
            $table->renameColumn('jumlah', 'count');
            $table->renameColumn('tanggal', 'date');
            $table->renameColumn('produksi_id', 'production_id');
        });

        Schema::table('pakan', function (Blueprint $table) {
            $table->renameColumn('dibuat_pada', 'created_at');
            $table->renameColumn('diperbarui_pada', 'updated_at');
            $table->renameColumn('jumlah_karung', 'amount_sacks');
            $table->renameColumn('jumlah_kg', 'amount_kg');
            $table->renameColumn('tanggal', 'date');
            $table->renameColumn('produksi_id', 'production_id');
        });

        Schema::table('produksi', function (Blueprint $table) {
            $table->renameColumn('dibuat_pada', 'created_at');
            $table->renameColumn('diperbarui_pada', 'updated_at');
            $table->renameColumn('tanggal_akhir', 'end_date');
            $table->renameColumn('jumlah_indukan', 'hen_count');
            $table->renameColumn('tanggal_mulai', 'start_date');
        });

        Schema::table('pembesaran', function (Blueprint $table) {
            $table->renameColumn('dibuat_pada', 'created_at');
            $table->renameColumn('diperbarui_pada', 'updated_at');
            $table->renameColumn('jumlah_siap', 'ready_count');
            $table->renameColumn('tanggal_siap', 'ready_date');
            $table->renameColumn('jenis_kelamin', 'gender');
            $table->renameColumn('jumlah_anak_ayam', 'chick_count');
            $table->renameColumn('tanggal_masuk', 'entry_date');
        });

        Schema::table('penetasan', function (Blueprint $table) {
            $table->renameColumn('dibuat_pada', 'created_at');
            $table->renameColumn('diperbarui_pada', 'updated_at');
            $table->renameColumn('jumlah_doc', 'doc_count');
            $table->renameColumn('jumlah_menetas', 'hatched_count');
            $table->renameColumn('tanggal_menetas', 'hatching_date');
            $table->renameColumn('jumlah_telur', 'egg_count');
            $table->renameColumn('tanggal_simpan_telur', 'egg_storage_date');
        });

        Schema::table('pengguna', function (Blueprint $table) {
            $table->renameColumn('diperbarui_pada', 'updated_at');
            $table->renameColumn('dibuat_pada', 'created_at');
            $table->renameColumn('token_ingat', 'remember_token');
            $table->renameColumn('surel_terverifikasi_pada', 'email_verified_at');
            $table->renameColumn('surel', 'email');
            $table->renameColumn('nama_pengguna', 'username');
            $table->renameColumn('nama', 'name');
            $table->dropColumn('peran');
            $table->renameColumn('kata_sandi', 'password');
            $table->enum('role', ['owner', 'operator'])->default('operator')->after('password');
        });

        // Reverse rename tables
        Schema::rename('kematian', 'deaths');
        Schema::rename('telur', 'eggs');
        Schema::rename('pakan', 'feeds');
        Schema::rename('produksi', 'productions');
        Schema::rename('pembesaran', 'growings');
        Schema::rename('penetasan', 'hatchings');
        Schema::rename('pengguna', 'users');
    }
};
