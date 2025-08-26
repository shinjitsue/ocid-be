<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createIndexIfNotExists('colleges', ['acronym', 'campus_id'], 'idx_colleges_acronym_campus');
        $this->createIndexIfNotExists('colleges', ['name'], 'idx_colleges_name');

        $this->createIndexIfNotExists('undergrads', ['program_name'], 'idx_undergrads_program_name');
        $this->createIndexIfNotExists('undergrads', ['acronym'], 'idx_undergrads_acronym');

        $this->createIndexIfNotExists('graduates', ['program_name'], 'idx_graduates_program_name');
        $this->createIndexIfNotExists('graduates', ['acronym'], 'idx_graduates_acronym');

        $this->createIndexIfNotExists('curriculum', ['file_type'], 'idx_curriculum_file_type');
        $this->createIndexIfNotExists('curriculum', ['program_type', 'created_at'], 'idx_curriculum_type_created');
        $this->createIndexIfNotExists('curriculum', ['program_id', 'program_type', 'created_at'], 'idx_curriculum_program_full');

        $this->createIndexIfNotExists('syllabus', ['file_type'], 'idx_syllabus_file_type');
        $this->createIndexIfNotExists('syllabus', ['program_type', 'created_at'], 'idx_syllabus_type_created');
        $this->createIndexIfNotExists('syllabus', ['program_id', 'program_type', 'created_at'], 'idx_syllabus_program_full');
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

    /**
     * Helper function to check and create index if it doesn't exist.
     */
    private function createIndexIfNotExists(string $table, array $columns, string $indexName): void
    {
        $exists = DB::selectOne("
            SELECT 1 FROM pg_indexes 
            WHERE tablename = ? AND indexname = ?
        ", [$table, $indexName]);

        if (!$exists) {
            Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        }
    }
};