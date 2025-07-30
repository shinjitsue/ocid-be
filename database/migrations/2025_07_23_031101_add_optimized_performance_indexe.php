<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Disable transactions for this migration since we're using CONCURRENTLY
     */
    public $withinTransaction = false;

    public function up(): void
    {
        // Covering indexes for most common queries
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_colleges_covering ON colleges (id, name, acronym, campus_id, logo_url, created_at)');

        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_undergrads_covering ON undergrads (id, program_name, acronym, college_id, created_at)');

        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_graduates_covering ON graduates (id, program_name, acronym, college_id, created_at)');

        // Partial indexes for file queries
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_curriculum_undergrad ON curriculum (program_id, created_at) WHERE program_type = \'undergrad\'');

        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_curriculum_graduate ON curriculum (program_id, created_at) WHERE program_type = \'graduate\'');

        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_syllabus_undergrad ON syllabus (program_id, created_at) WHERE program_type = \'undergrad\'');

        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_syllabus_graduate ON syllabus (program_id, created_at) WHERE program_type = \'graduate\'');

        // Functional indexes for file counts (these don't need CONCURRENTLY since they're smaller tables)
        Schema::table('curriculum', function (Blueprint $table) {
            $table->index(['program_type', 'program_id'], 'idx_curriculum_type_program');
        });

        Schema::table('syllabus', function (Blueprint $table) {
            $table->index(['program_type', 'program_id'], 'idx_syllabus_type_program');
        });
    }

    public function down(): void
    {
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_colleges_covering');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_undergrads_covering');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_graduates_covering');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_curriculum_undergrad');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_curriculum_graduate');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_syllabus_undergrad');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_syllabus_graduate');

        Schema::table('curriculum', function (Blueprint $table) {
            $table->dropIndex('idx_curriculum_type_program');
        });

        Schema::table('syllabus', function (Blueprint $table) {
            $table->dropIndex('idx_syllabus_type_program');
        });
    }
};
