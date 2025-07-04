<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup {--fresh : Drop all tables before importing} {--dump : Generate dump from current database before setup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the database using the SQL dump file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database setup...');

        // Get database configuration
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if ($connection === 'pgsql') {
            $this->setupPostgresql($config);
        } elseif ($connection === 'mysql' || $connection === 'mariadb') {
            $this->setupMysql($config);
        } else {
            $this->error("Unsupported database driver: {$connection}");
            return 1;
        }

        if ($this->option('dump')) {
            $this->call('db:dump', ['--overwrite' => true]);
        }

        $this->info('Database setup completed successfully!');
        return 0;
    }

    /**
     * Set up PostgreSQL database
     */
    protected function setupPostgresql(array $config)
    {
        $dumpPath = database_path('dump/database_dump.sql');

        if (!File::exists($dumpPath)) {
            $this->error("Database dump file not found: {$dumpPath}");
            return;
        }

        // Check if the --fresh option is used
        if ($this->option('fresh')) {
            if ($this->confirm('This will drop all tables in the database. Continue?', false)) {
                $this->info('Dropping all tables...');
                $this->runMigrationFresh();
            } else {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('Importing database dump...');

        $command = [
            'psql',
            '-h', $config['host'],
            '-p', $config['port'],
            '-U', $config['username'],
            '-d', $config['database'],
            '-f', $dumpPath
        ];

        $this->info('Running: ' . implode(' ', $command));

        // Set PGPASSWORD environment variable for password
        $env = ['PGPASSWORD' => $config['password']];

        $process = new Process($command, null, $env);
        $process->setTimeout(3600); // 1 hour timeout

        try {
            $process->mustRun(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    $this->error($buffer);
                } else {
                    $this->line($buffer);
                }
            });
            $this->info('Database import completed successfully.');
        } catch (ProcessFailedException $e) {
            $this->error('Failed to import database dump.');
            $this->error($e->getMessage());
        }
    }

    /**
     * Set up MySQL/MariaDB database
     */
    protected function setupMysql(array $config)
    {
        $dumpPath = database_path('dump/database_dump.sql');

        if (!File::exists($dumpPath)) {
            $this->error("Database dump file not found: {$dumpPath}");
            return;
        }

        // Check if the --fresh option is used
        if ($this->option('fresh')) {
            if ($this->confirm('This will drop all tables in the database. Continue?', false)) {
                $this->info('Dropping all tables...');
                $this->runMigrationFresh();
            } else {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('Importing database dump...');

        $command = [
            'mysql',
            '-h', $config['host'],
            '-P', $config['port'],
            '-u', $config['username'],
            '-p' . $config['password'],
            $config['database'],
            '-e', "source {$dumpPath}"
        ];

        $this->info('Running MySQL import...');

        $process = new Process($command);
        $process->setTimeout(3600); // 1 hour timeout

        try {
            $process->mustRun(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    $this->error($buffer);
                } else {
                    $this->line($buffer);
                }
            });
            $this->info('Database import completed successfully.');
        } catch (ProcessFailedException $e) {
            $this->error('Failed to import database dump.');
            $this->error($e->getMessage());
        }
    }

    /**
     * Run migration:fresh command
     */
    protected function runMigrationFresh()
    {
        $this->call('migrate:fresh');
    }
}
