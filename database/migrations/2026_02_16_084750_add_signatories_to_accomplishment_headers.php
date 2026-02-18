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
        Schema::table('accomplishment_headers', function (Blueprint $table) {
            $table->string('prepared_by')->nullable();
            $table->string('noted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accomplishment_headers', function (Blueprint $table) {
            $table->dropColumn(['prepared_by', 'noted_by']);
        });
    }
};
