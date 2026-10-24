<?php

namespace App\Console\Commands;

use App\User;
use Database\Seeders\LocalDevelopmentSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SeedLocalDatabase extends Command
{
    protected $signature = 'db:seed-local {--dump= : Import the selected user's games and filters from a phpMyAdmin SQL dump} {--source-user=4 : User ID in the SQL dump to import}';
    protected $description = 'Reset the local demo library, optionally importing one account from a legacy SQL dump';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('Local demo seeding is disabled in production.');
            return self::FAILURE;
        }

        if ($this->option('dump') && (!is_file($this->option('dump')) || !is_readable($this->option('dump')))) {
            $this->error('The SQL dump path must point to a readable file.');
            return self::FAILURE;
        }

        $this->call('db:seed', ['--class' => LocalDevelopmentSeeder::class, '--force' => true]);

        if ($this->option('dump')) {
            $path = $this->option('dump');
            try {
                $this->importDump($path, (int) $this->option('source-user'));
            } catch (RuntimeException $exception) {
                $this->error($exception->getMessage());
                return self::FAILURE;
            }
        }

        $this->info('Local demo account is ready: '.LocalDevelopmentSeeder::EMAIL.' (password: password)');
        return self::SUCCESS;
    }

    private function importDump(string $path, int $sourceUserId): void
    {
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException('Could not read the SQL dump.');
        }

        // Only parse INSERT rows for games and filters. Never execute the dump's DDL or other SQL.
        $user = User::where('email', LocalDevelopmentSeeder::EMAIL)->firstOrFail();
        $counts = ['games' => 0, 'filters' => 0];
        DB::transaction(function () use ($sql, $sourceUserId, $user, &$counts): void {
            foreach (['games', 'filters'] as $table) {
                foreach ($this->insertRows($sql, $table) as [$columns, $values]) {
                    $row = array_combine($columns, $values);
                    if ((int) ($row['userId'] ?? 0) !== $sourceUserId) {
                        continue;
                    }

                    unset($row['id']);
                    $row['userId'] = $user->id;
                    DB::table($table)->insert($row);
                    $counts[$table]++;
                }
            }

            if ($counts['games'] === 0) {
                throw new RuntimeException("No game rows found for source user {$sourceUserId} in the dump.");
            }
        });

        $this->info("Imported {$counts['games']} games and {$counts['filters']} filters from source user {$sourceUserId}.");
    }

    /** @return iterable<array{array<int, string>, array<int, mixed>}> */
    private function insertRows(string $sql, string $table): iterable
    {
        $pattern = '/INSERT\s+INTO\s+`'.preg_quote($table, '/').'`\s*\((.*?)\)\s*VALUES\s*(.*?);/si';
        if (!preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER)) {
            return [];
        }

        $rows = [];
        foreach ($matches as $match) {
            preg_match_all('/`([^`]+)`/', $match[1], $columnMatches);
            $columns = $columnMatches[1];
            foreach ($this->parseTuples($match[2]) as $tuple) {
                if (count($tuple) !== count($columns)) {
                    throw new RuntimeException("Malformed INSERT row in {$table}.");
                }
                $rows[] = [$columns, $tuple];
            }
        }
        return $rows;
    }

    /** @return iterable<array<int, mixed>> */
    private function parseTuples(string $input): iterable
    {
        $length = strlen($input);
        for ($i = 0; $i < $length; $i++) {
            if ($input[$i] !== '(') continue;
            $values = [];
            $value = '';
            $quoted = false;
            for ($i++; $i < $length; $i++) {
                $char = $input[$i];
                if ($quoted && $char === '\\' && $i + 1 < $length) {
                    $value .= $input[++$i];
                } elseif ($char === "'") {
                    if ($quoted && ($input[$i + 1] ?? '') === "'") {
                        $value .= "'";
                        $i++;
                    } else {
                        $quoted = !$quoted;
                    }
                } elseif (!$quoted && ($char === ',' || $char === ')')) {
                    $trimmed = trim($value);
                    $values[] = strtoupper($trimmed) === 'NULL' ? null : $trimmed;
                    $value = '';
                    if ($char === ')') {
                        yield $values;
                        break;
                    }
                } else {
                    $value .= $char;
                }
            }
        }
    }
}
