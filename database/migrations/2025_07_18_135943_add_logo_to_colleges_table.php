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
        Schema::table('colleges', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('acronym');
            $table->string('logo_url')->nullable()->after('logo_path');
            $table->string('logo_name')->nullable()->after('logo_url');
            $table->string('logo_type')->nullable()->after('logo_name');
            $table->integer('logo_size')->nullable()->after('logo_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'logo_url', 'logo_name', 'logo_type', 'logo_size']);
        });
    }
};
