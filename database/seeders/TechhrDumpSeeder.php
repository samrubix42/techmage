<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Loads the data rows from the production phpMyAdmin dump
 * (database/seeders/data/growtfce_techhr.sql) into the migrated schema.
 *
 * Only INSERT statements are executed; table structure comes from the
 * migrations, so new columns (e.g. users.salary) keep their defaults.
 */
class TechhrDumpSeeder extends Seeder
{
    public const DUMP_PATH = 'seeders/data/growtfce_techhr.sql';

    /**
     * Tables in the dump whose rows should not be imported.
     */
    protected array $skipTables = [
        'migrations',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
    ];

    public function run(): void
    {
        $path = database_path(self::DUMP_PATH);

        if (! is_file($path)) {
            $this->command?->warn("SQL dump not found at {$path}; skipping.");

            return;
        }

        $statements = $this->extractInserts(file_get_contents($path));
        $tables = array_unique(array_column($statements, 'table'));

        Schema::disableForeignKeyConstraints();

        try {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }

            foreach ($statements as $statement) {
                DB::unprepared($statement['sql']);
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->command?->info('Imported '.count($statements).' insert statements into: '.implode(', ', $tables));

        // Ensure the demo logins exist with a known password:
        // admin@admin.com / password and employee@employee.com / password.
        $this->call(AdminSeeder::class);
    }

    /**
     * Pull every top-level INSERT statement out of the dump.
     *
     * @return array<int, array{table: string, sql: string}>
     */
    protected function extractInserts(string $sql): array
    {
        // Split on "\n" only: a non-unicode /\R/ also matches byte 0x85,
        // which occurs inside multibyte UTF-8 characters (e.g. Hindi text).
        $lines = explode("\n", str_replace("\r\n", "\n", $sql));
        $statements = [];
        $buffer = null;
        $table = null;
        $count = count($lines);

        for ($i = 0; $i < $count; $i++) {
            $line = $lines[$i];

            if ($buffer === null) {
                if (preg_match('/^INSERT INTO `([^`]+)`/', $line, $m)) {
                    $table = $m[1];
                    $buffer = '';
                } else {
                    continue;
                }
            }

            $buffer .= $line."\n";

            // A statement ends on a line ending in ";" that is followed by a
            // blank line, a comment, another statement, or end of file.
            $next = $lines[$i + 1] ?? '';
            if (str_ends_with(rtrim($line), ';')
                && ($next === '' || preg_match('/^(--|INSERT |ALTER |CREATE |COMMIT|\/\*)/', $next))) {
                if (! in_array($table, $this->skipTables, true)) {
                    $statements[] = ['table' => $table, 'sql' => $buffer];
                }
                $buffer = null;
                $table = null;
            }
        }

        return $statements;
    }
}
