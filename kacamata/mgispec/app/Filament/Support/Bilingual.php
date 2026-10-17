<?php

namespace App\Filament\Support;

use App\Support\I18n;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

/**
 * Side-by-side ID / PH inputs for a bilingual JSON column ("field.id", "field.ph").
 * Indonesian is required when $required; Philippines falls back to Indonesian when left empty.
 */
class Bilingual
{
    public static function text(string $field, string $label, bool $required = false, ?int $maxLength = 255): Grid
    {
        return self::make($field, $label, fn (string $path) => TextInput::make($path)->maxLength($maxLength), $required);
    }

    public static function textarea(string $field, string $label, bool $required = false, int $rows = 3): Grid
    {
        return self::make($field, $label, fn (string $path) => Textarea::make($path)->rows($rows), $required);
    }

    public static function rich(string $field, string $label, bool $required = false): Grid
    {
        return self::make($field, $label, fn (string $path) => RichEditor::make($path)
            ->toolbarButtons(['bold', 'italic', 'underline', 'h2', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo']), $required, 1);
    }

    private static function make(string $field, string $label, callable $factory, bool $required, ?int $columns = null): Grid
    {
        $inputs = [];
        $locales = I18n::enabledLocales();

        foreach ($locales as $code => $meta) {
            /** @var Component $input */
            $input = $factory("{$field}.{$code}")->label("{$label} {$meta['flag']} {$meta['short']}");

            if ($code === 'id') {
                $input->required($required);
            } else {
                $input->placeholder('Kosongkan = pakai teks Bahasa Indonesia');
            }

            $inputs[] = $input;
        }

        return Grid::make($columns ?? count($locales))->schema($inputs);
    }
}
