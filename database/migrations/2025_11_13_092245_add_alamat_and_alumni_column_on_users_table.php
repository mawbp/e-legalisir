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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('alamat_id')->nullable()->after('email');
            $table->unsignedBigInteger('alumni_id')->nullable()->after('alamat_id');

            $table->foreign('alamat_id')->references('id')->on('alamat')->onDelete('set null');
            $table->foreign('alumni_id')->references('id')->on('alumni')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['alamat_id']);
            $table->dropForeign(['alumni_id']);
            $table->dropColumn('alamat_id');
            $table->dropColumn('alumni_id');
        });
    }
};
