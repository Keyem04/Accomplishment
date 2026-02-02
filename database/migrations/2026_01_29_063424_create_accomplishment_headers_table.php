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
        Schema::create('accomplishment_headers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id'); // ID from external DB
            $table->integer('reporting_month');
            $table->integer('reporting_year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accomplishment_headers');
    }
};
