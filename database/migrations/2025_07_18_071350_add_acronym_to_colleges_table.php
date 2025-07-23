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
            $table->string('acronym', 10)->nullable()->after('name');
            $table->index('acronym');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropIndex(['acronym']);
            $table->dropColumn('acronym');
        });
    }
};
