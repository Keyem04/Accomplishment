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
            if (!Schema::hasColumn('accomplishment_headers', 'status')) {
                $table->string('status')
                    ->default('draft')
                    ->after('reporting_year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accomplishment_headers', function (Blueprint $table) {
           if (Schema::hasColumn('accomplishment_headers', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
