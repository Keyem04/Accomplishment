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
        Schema::table('accomplishment_details', function (Blueprint $table) {
            $table->unsignedInteger('created_by')
                ->nullable()
                ->after('ppa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accomplishment_details', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};
