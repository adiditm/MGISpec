<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Media
{
    /** Resolve an uploaded path (public disk) or an absolute URL into a browser URL. */
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
