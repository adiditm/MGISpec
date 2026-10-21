<?php

namespace App\Filament\Pages;

use App\Filament\Support\Bilingual;
use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Website';

    protected static ?string $title = 'Pengaturan Website';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::values());
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Section::make('Kontak')->columns(2)->schema([
                TextInput::make('whatsapp')->label('Nomor WhatsApp CS')->helperText('Format internasional tanpa + / spasi, contoh 6281234567890')
                    ->required()->regex('/^[0-9]{8,15}$/'),
                TextInput::make('phone')->label('Telepon (tampilan)'),
                TextInput::make('email')->email(),
                TextInput::make('address')->label('Alamat / area'),
            ]),
            Section::make('Media Sosial')->columns(2)->schema([
                TextInput::make('facebook')->url(),
                TextInput::make('instagram')->url(),
            ]),
            Section::make('Footer')->schema([
                Bilingual::textarea('footer_about', 'Profil singkat', rows: 3),
                TextInput::make('copyright')->label('Teks copyright'),
            ]),
            Section::make('Hero Slider')->columns(2)->schema([
                Select::make('slider_effect')->label('Efek transisi umum')->required()->options(\App\Models\Slider::EFFECTS)
                    ->helperText('Dipakai semua banner yang di menu Hero Slider diset "Ikuti pengaturan umum". Pilih "Random" supaya efeknya acak.'),
                TextInput::make('slider_delay')->label('Durasi per slide (detik)')->numeric()->minValue(2)->maxValue(20)->required(),
            ]),
        ]);
    }

    public function save(): void
    {
        Setting::put($this->form->getState());

        Notification::make()->title('Pengaturan tersimpan')->success()->send();
    }
}
