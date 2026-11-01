<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\Field;

/**
 * Visual Font Awesome icon picker: a searchable grid of curated icons (see App\Support\IconLibrary).
 * Stores the full class, e.g. "fa-solid fa-glasses". The icon list and styles come from
 * resources/views/filament/icon-picker-assets.blade.php, loaded once in the panel head.
 */
class IconPicker extends Field
{
    protected string $view = 'filament.forms.icon-picker';
}
