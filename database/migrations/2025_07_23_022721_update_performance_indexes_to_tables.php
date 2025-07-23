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
        // Additional indexes for faster joins and lookups
        Schema::table('colleges', function (Blueprint $table) {
            $table->index(['acronym', 'campus_id'], 'idx_colleges_acronym_campus');
            $table->index('name', 'idx_colleges_name');
        });

        Schema::table('undergrads', function (Blueprint $table) {
            $table->index('program_name', 'idx_undergrads_program_name');
            $table->index('acronym', 'idx_undergrads_acronym');
        });

        Schema::table('graduates', function (Blueprint $table) {
            $table->index('program_name', 'idx_graduates_program_name');
            $table->index('acronym', 'idx_graduates_acronym');
        });

        Schema::table('curriculum', function (Blueprint $table) {
            $table->index('file_type', 'idx_curriculum_file_type');
            $table->index(['program_type', 'created_at'], 'idx_curriculum_type_created');
        });

        Schema::table('syllabus', function (Blueprint $table) {
            $table->index('file_type', 'idx_syllabus_file_type');
            $table->index(['program_type', 'created_at'], 'idx_syllabus_type_created');
        });

        // Composite indexes for complex queries
        Schema::table('curriculum', function (Blueprint $table) {
            $table->index(['program_id', 'program_type', 'created_at'], 'idx_curriculum_program_full');
        });

        Schema::table('syllabus', function (Blueprint $table) {
            $table->index(['program_id', 'program_type', 'created_at'], 'idx_syllabus_program_full');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('colleges', function (Blueprint $table) {
            $table->dropIndex('idx_colleges_acronym_campus');
            $table->dropIndex('idx_colleges_name');
        });

        Schema::table('undergrads', function (Blueprint $table) {
            $table->dropIndex('idx_undergrads_program_name');
            $table->dropIndex('idx_undergrads_acronym');
        });

        Schema::table('graduates', function (Blueprint $table) {
            $table->dropIndex('idx_graduates_program_name');
            $table->dropIndex('idx_graduates_acronym');
        });

        Schema::table('curriculum', function (Blueprint $table) {
            $table->dropIndex('idx_curriculum_file_type');
            $table->dropIndex('idx_curriculum_type_created');
            $table->dropIndex('idx_curriculum_program_full');
        });

        Schema::table('syllabus', function (Blueprint $table) {
            $table->dropIndex('idx_syllabus_file_type');
            $table->dropIndex('idx_syllabus_type_created');
            $table->dropIndex('idx_syllabus_program_full');
        });
    }
};