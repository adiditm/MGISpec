<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Client feedback 2026-11-20: drop the "80%" figure everywhere blue-light filtering is mentioned (id / en / cn),
 * keeping the rest of the wording. Works on the live database values (so CMS edits are kept), only touches text
 * next to a blue-light phrase, backs up every changed product to product_content_backups first; down() restores them.
 */
return new class extends Migration
{
    private const BATCH = 'remove-blue-light-80-2026-11-20';

    private const COLUMNS = ['short_description', 'description', 'highlights', 'technologies', 'frame_technologies', 'lens_technologies'];

    private const PCT = '80\s*(?:%|％)';

    private const QUALIFIER = '(?:up\s+to|hingga|sampai|高达|最高可达|最高|可达)';

    private const BLUE = '(?:blue[\s-]*light(?:\s+filtering)?|penyaringan\s+(?:sinar|cahaya)\s+biru|蓝光过滤)';

    public function up(): void
    {
        if (! Schema::hasTable('product_content_backups')) {
            Schema::create('product_content_backups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('batch', 60)->index();
                $table->longText('payload');
                $table->timestamp('created_at')->nullable();
            });
        }

        $columns = array_values(array_filter(self::COLUMNS, fn ($c) => Schema::hasColumn('products', $c)));

        foreach (DB::table('products')->orderBy('id')->get() as $row) {
            $old = [];
            $update = [];

            foreach ($columns as $col) {
                if ($row->{$col} === null || $row->{$col} === '') {
                    continue;
                }
                $value = json_decode($row->{$col}, true);
                if ($value === null) {
                    continue;
                }
                $clean = $this->clean($value);
                if ($clean !== $value) {
                    $old[$col] = $row->{$col};
                    $update[$col] = json_encode($clean, JSON_UNESCAPED_UNICODE);
                }
            }

            if ($update === []) {
                continue;
            }

            if (! DB::table('product_content_backups')->where('product_id', $row->id)->where('batch', self::BATCH)->exists()) {
                DB::table('product_content_backups')->insert([
                    'product_id' => $row->id,
                    'batch' => self::BATCH,
                    'payload' => json_encode($old, JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                ]);
            }

            DB::table('products')->where('id', $row->id)->update($update + ['updated_at' => now()]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('product_content_backups')) {
            return;
        }

        foreach (DB::table('product_content_backups')->where('batch', self::BATCH)->get() as $backup) {
            $old = json_decode($backup->payload, true);
            if (is_array($old)) {
                DB::table('products')->where('id', $backup->product_id)->update($old + ['updated_at' => now()]);
            }
        }
        DB::table('product_content_backups')->where('batch', self::BATCH)->delete();
    }

    /** Walks a decoded column value; strings get the inline rules, blue-light technology items also lose a leading "80%" line. */
    public function clean(mixed $value): mixed
    {
        if (is_string($value)) {
            return $this->cleanText($value);
        }
        if (! is_array($value)) {
            return $value;
        }

        $isBlueItem = isset($value['title']) && $this->mentionsBlue($value['title']);

        foreach ($value as $key => $child) {
            if ($isBlueItem && $key === 'description') {
                $child = $this->dropPercentLines($child);
            }
            $value[$key] = $this->clean($child);
        }

        return $value;
    }

    private function mentionsBlue(mixed $title): bool
    {
        foreach ((array) $title as $text) {
            if (is_string($text) && preg_match('/blue|biru|蓝光/iu', $text)) {
                return true;
            }
        }

        return false;
    }

    /** Removes a line that is nothing but "80%" / "Up to 80%" / "HINGGA 80%" / "高达80%" inside a blue-light item. */
    private function dropPercentLines(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($v) => $this->dropPercentLines($v), $value);
        }
        if (! is_string($value)) {
            return $value;
        }

        return preg_replace('/^[ \t]*'.self::QUALIFIER.'?[ \t]*'.self::PCT.'[ \t]*(?:\r?\n|$)/imu', '', $value);
    }

    private function cleanText(string $text): string
    {
        $pct = self::PCT;
        $q = self::QUALIFIER;
        $blue = self::BLUE;

        $rules = [
            // "Blue Light Filtering – Up to 80%", "PENYARINGAN CAHAYA BIRU – HINGGA 80%", "蓝光过滤——最高 80%", "蓝光过滤 – 高达80%"
            "/({$blue})\s*[–—-]+\s*{$q}?\s*{$pct}/iu" => '$1',
            // "Blue Light Filtering (80%)", "(up to 80%)"
            "/({$blue})\s*[(（]\s*{$q}?\s*{$pct}\s*[)）]/iu" => '$1',
            // "Penyaringan Sinar Biru hingga 80%", "Blue Light Filtering up to 80%"
            "/({$blue})\s+{$q}\s*{$pct}/iu" => '$1',
            // "filter up to 80% of blue light", "menyaring hingga 80% sinar biru"
            "/\b{$q}\s*{$pct}\s+(?:of\s+)?(?=(?:the\s+)?blue|(?:sinar|cahaya)\s+biru)/iu" => '',
            // "过滤高达80%的蓝光"
            "/{$q}?\s*{$pct}\s*的?(?=蓝光)/u" => '',
        ];

        return preg_replace(array_keys($rules), array_values($rules), $text);
    }
};
