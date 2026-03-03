<?php

namespace App\Console\Commands;

use App\Models\Brand;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportBrands extends Command
{
    protected $signature   = 'brands:import {file : Full path to the CSV file}';
    protected $description = 'Import brands and sub-brands (models) from a CSV file';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        // ----------------------------------------------------------------
        // Read CSV
        // ----------------------------------------------------------------
        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // skip header row

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue;

            $subBrand   = trim(html_entity_decode($row[0], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $parentName = trim(html_entity_decode($row[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            if ($subBrand === '' || $parentName === '') continue;

            $rows[] = ['sub' => $subBrand, 'parent' => $parentName];
        }
        fclose($handle);

        $this->info("Read " . count($rows) . " rows from CSV.");

        // ----------------------------------------------------------------
        // Step 1 — Insert all unique PARENT brands first
        // ----------------------------------------------------------------
        $parentNames = array_unique(array_column($rows, 'parent'));
        sort($parentNames);

        $this->info("Inserting " . count($parentNames) . " parent brands...");
        $bar = $this->output->createProgressBar(count($parentNames));
        $bar->start();

        $parentMap = []; // name => id

        foreach ($parentNames as $name) {
            $slug = $this->uniqueSlug($name);

            $brand = Brand::firstOrCreate(
                ['slug' => $slug],
                [
                    'name'       => $name,
                    'slug'       => $slug,
                    'parent_id'  => null,
                    'is_active'  => true,
                    'sort_order' => 0,
                ]
            );

            $parentMap[$name] = $brand->id;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // ----------------------------------------------------------------
        // Step 2 — Insert SUB-BRANDS linked to their parent
        // ----------------------------------------------------------------
        $this->info("Inserting " . count($rows) . " sub-brands / models...");
        $bar2 = $this->output->createProgressBar(count($rows));
        $bar2->start();

        $inserted = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            $parentId = $parentMap[$row['parent']] ?? null;

            if (!$parentId) {
                $skipped++;
                $bar2->advance();
                continue;
            }

            $slug = $this->uniqueSlug($row['sub'], $parentId);

            Brand::firstOrCreate(
                ['slug' => $slug],
                [
                    'name'       => $row['sub'],
                    'slug'       => $slug,
                    'parent_id'  => $parentId,
                    'is_active'  => true,
                    'sort_order' => 0,
                ]
            );

            $inserted++;
            $bar2->advance();
        }

        $bar2->finish();
        $this->newLine(2);

        $this->table(
            ['Metric', 'Count'],
            [
                ['Parent brands inserted', count($parentNames)],
                ['Sub-brands / models inserted', $inserted],
                ['Skipped (no parent found)', $skipped],
                ['Total rows processed', count($rows)],
            ]
        );

        $this->info('Import complete!');
        return self::SUCCESS;
    }

    // ----------------------------------------------------------------
    // Generate a unique slug (appends parent_id suffix if needed)
    // ----------------------------------------------------------------
    private function uniqueSlug(string $name, ?int $parentId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (Brand::where('slug', $slug)->exists()) {
            // If the existing record belongs to a different parent, make it unique
            $existing = Brand::where('slug', $slug)->first();
            if ($existing->parent_id === $parentId) {
                break; // same parent — firstOrCreate will handle it
            }
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
