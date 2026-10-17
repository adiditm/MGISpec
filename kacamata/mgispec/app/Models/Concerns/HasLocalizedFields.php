<?php

namespace App\Models\Concerns;

use App\Support\I18n;

/**
 * Bilingual JSON columns stored as {"id": "...", "ph": "..."}.
 * Filament edits them via "field.id" / "field.ph"; the front-end reads them via t().
 */
trait HasLocalizedFields
{
    public function t(string $field, ?string $locale = null): mixed
    {
        return I18n::pick($this->{$field}, $locale);
    }
}
