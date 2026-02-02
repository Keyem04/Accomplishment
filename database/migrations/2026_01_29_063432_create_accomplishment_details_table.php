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
        Schema::create('accomplishment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('header_id')->nullable()->constrained('accomplishment_headers')->onDelete('cascade');
            $table->date('date');
            $table->string('title_of_accomplishment');
            $table->text('brief_description')->nullable();
            $table->text('scope')->nullable();
            $table->text('results')->nullable();
            $table->string('mov')->nullable();
            $table->unsignedBigInteger('ppa_id')->nullable(); // reference to external PPA table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accomplishment_details');
    }
};
