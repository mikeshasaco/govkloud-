<?php

namespace App\Console\Commands;

use App\Models\Challenge;
use Illuminate\Console\Command;

class ImportChallenges extends Command
{
    protected $signature = 'challenges:import {file : Path to JSON file with challenge data}';
    protected $description = 'Import challenges from a JSON file (upserts by slug)';

    public function handle(): int
    {
        $path = $this->argument('file');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $json = file_get_contents($path);
        $challenges = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return 1;
        }

        if (!is_array($challenges)) {
            $this->error('JSON must be an array of challenge objects.');
            return 1;
        }

        $created = 0;
        $updated = 0;

        foreach ($challenges as $index => $data) {
            if (empty($data['slug'])) {
                $this->warn("Skipping entry #{$index}: missing slug.");
                continue;
            }

            $existing = Challenge::where('slug', $data['slug'])->first();

            Challenge::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            if ($existing) {
                $updated++;
                $this->line("  ✏️  Updated: {$data['title']} ({$data['slug']})");
            } else {
                $created++;
                $this->line("  ✅ Created: {$data['title']} ({$data['slug']})");
            }
        }

        $this->newLine();
        $this->info("Done! Created: {$created}, Updated: {$updated}, Total: " . ($created + $updated));

        return 0;
    }
}
