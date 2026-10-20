<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The site's second language is now English, but older CMS entries were saved with a Filipino ("ph")
 * key, so the CMS still showed "ID & PH". Move every "ph" text to "en" (where English is still empty)
 * and drop the "ph" key from all translatable columns.
 */
return new class extends Migration
{
    private const COLUMNS = [
        'settings' => ['value'],
        'categories' => ['name'],
        'products' => ['name', 'short_description', 'description', 'highlights', 'specs', 'technologies'],
        'promo_banners' => ['title', 'description'],
        'sliders' => ['badge', 'title', 'subtitle', 'cta_text', 'cta2_text', 'desktop_image', 'mobile_image'],
    ];

    public function up(): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $columns = array_values(array_filter($columns, fn ($c) => Schema::hasColumn($table, $c)));

            foreach (DB::table($table)->get(array_merge(['id'], $columns)) as $row) {
                $changes = [];
                foreach ($columns as $column) {
                    $decoded = json_decode((string) $row->{$column}, true);
                    if (! is_array($decoded)) {
                        continue;
                    }
                    $fixed = $this->fix($decoded);
                    if ($fixed !== $decoded) {
                        $changes[$column] = json_encode($fixed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
                if ($changes) {
                    DB::table($table)->where('id', $row->id)->update($changes);
                }
            }
        }

        Cache::forget('site_settings');
    }

    public function down(): void
    {
        // Nothing to undo: the Filipino texts now live under "en".
    }

    private function fix(array $value): array
    {
        if (array_key_exists('ph', $value) && ! array_is_list($value)) {
            if (blank($value['en'] ?? null)) {
                $value['en'] = $value['ph'];
            }
            unset($value['ph']);
        }

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->fix($item);
            }
        }

        return $value;
    }
};
