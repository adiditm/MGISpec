<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * On the shared-hosting dev server public/storage is a real folder (no symlink), so files written to
 * storage/app/public are not served. Copy every certificate file already stored there into
 * public/storage so existing certificates (the two samples) show up. No-op when public/storage is a symlink.
 */
return new class extends Migration
{
    public function up(): void
    {
        $public = public_path('storage');
        $source = storage_path('app/public/certificates');

        if (! Schema::hasTable('certificates') || is_link($public) || ! is_dir($public) || ! is_dir($source)
            || realpath($public) === realpath(storage_path('app/public'))) {
            return;
        }

        File::ensureDirectoryExists($public.'/certificates');
        foreach (File::files($source) as $file) {
            File::copy($file->getPathname(), $public.'/certificates/'.$file->getFilename());
        }
    }

    public function down(): void
    {
        // Intentionally empty: the copied files are harmless.
    }
};
