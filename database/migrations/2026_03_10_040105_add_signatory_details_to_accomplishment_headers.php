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
            $table->string('signatory_type')->default('prepared_by')->after('prepared_by');
            $table->string('prepared_by_position', 150)->nullable()->after('signatory_type');
            $table->string('noted_by_position', 150)->nullable()->after('noted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accomplishment_headers', function (Blueprint $table) {
                $table->dropColumn([
                'signatory_type',
                'prepared_by_position',
                'noted_by_position',
            ]);
        });
    }
};
