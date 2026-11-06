<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/** The video block between the hero slider and the "Material Canggih" section on the home page. */
class HomeVideo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';

    protected static ?string $navigationGroup = 'Beranda';

    protected static ?string $navigationLabel = 'Video Beranda';

    protected static ?string $title = 'Video Beranda';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'video-beranda';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $values = Setting::values();

        $this->form->fill([
            'home_video_enabled' => ($values['home_video_enabled'] ?? true) !== false && ($values['home_video_enabled'] ?? true) !== '0',
            'home_video_path' => $values['home_video_path'] ?? null,
            'home_video_poster' => $values['home_video_poster'] ?? null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Section::make('Video di Beranda')
                ->description('Tampil sebagai blok sendiri di bawah slider utama dan di atas bagian "Material Canggih". Video berjalan otomatis tanpa suara saat terlihat di layar; pengunjung bisa mengetuk video untuk menyalakan suara.')
                ->schema([
                    Toggle::make('home_video_enabled')->label('Tampilkan video di beranda')->default(true),
                    FileUpload::make('home_video_path')->label('File video (MP4)')
                        ->acceptedFileTypes(['video/mp4'])
                        ->directory('videos')
                        ->maxSize(102400)
                        ->helperText('Format MP4 (H.264), maksimal 100 MB. Rasio lebar (misalnya 2430×540) paling pas dengan bloknya. Kosongkan untuk menyembunyikan blok video.'),
                    FileUpload::make('home_video_poster')->label('Gambar sampul (opsional)')
                        ->image()
                        ->directory('videos')
                        ->maxSize(4096)
                        ->helperText('Gambar yang tampil sebelum video diputar. Kalau kosong, memakai frame pertama video.'),
                ]),
        ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::put([
            'home_video_enabled' => (bool) ($state['home_video_enabled'] ?? true),
            'home_video_path' => $state['home_video_path'] ?? null,
            'home_video_poster' => $state['home_video_poster'] ?? null,
        ]);

        Notification::make()->title('Video beranda tersimpan')->success()->send();
    }
}
