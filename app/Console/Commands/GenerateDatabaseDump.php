<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GenerateDatabaseDump extends Command
{
    protected $signature = 'db:dump {--overwrite : Overwrite existing dump file}';
    protected $description = 'Generate a new database dump file from current database';

    public function handle()
    {
        $this->info('Generating database dump...');

        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        $dumpPath = database_path('dump/database_dump.sql');

        // Check if dump file exists
        if (File::exists($dumpPath) && !$this->option('overwrite')) {
            if (!$this->confirm('Dump file already exists. Overwrite?', false)) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        if ($connection === 'pgsql') {
            $this->generatePostgresqlDump($config, $dumpPath);
        } elseif ($connection === 'mysql' || $connection === 'mariadb') {
            $this->generateMysqlDump($config, $dumpPath);
        } else {
            $this->error("Unsupported database driver: {$connection}");
            return 1;
        }

        $this->info('Database dump generated successfully!');
        return 0;
    }

    protected function generatePostgresqlDump(array $config, string $dumpPath)
    {
        $command = [
            'pg_dump',
            '-h', $config['host'],
            '-p', $config['port'],
            '-U', $config['username'],
            '-d', $config['database'],
            '--clean',
            '--create',
            '--if-exists',
            '-f', $dumpPath
        ];

        $env = ['PGPASSWORD' => $config['password']];
        $this->executeDumpCommand($command, $env);
    }

    protected function generateMysqlDump(array $config, string $dumpPath)
    {
        $command = [
            'mysqldump',
            '-h', $config['host'],
            '-P', $config['port'],
            '-u', $config['username'],
            '-p' . $config['password'],
            '--routines',
            '--triggers',
            '--single-transaction',
            $config['database']
        ];

        $this->executeDumpCommand($command, [], $dumpPath);
    }

    protected function executeDumpCommand(array $command, array $env = [], string $outputFile = null)
    {
        $this->info('Running: ' . implode(' ', array_slice($command, 0, -2)) . ' [credentials hidden]');

        $process = new Process($command, null, $env);
        $process->setTimeout(3600);

        try {
            if ($outputFile && strpos(implode(' ', $command), '-f') === false) {
                // For MySQL, redirect output to file
                $process->mustRun();
                File::put($outputFile, $process->getOutput());
            } else {
                // For PostgreSQL, output is already directed to file via -f flag
                $process->mustRun(function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        $this->error($buffer);
                    } else {
                        $this->line($buffer);
                    }
                });
            }
        } catch (ProcessFailedException $e) {
            $this->error('Failed to generate database dump.');
            $this->error($e->getMessage());
            throw $e;
        }
    }
}
