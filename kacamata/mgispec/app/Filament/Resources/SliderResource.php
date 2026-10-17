<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Support\Bilingual;
use App\Models\Slider;
use App\Support\I18n;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Beranda';

    protected static ?string $modelLabel = 'Slide';

    protected static ?string $pluralModelLabel = 'Hero Slider';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $localeTabs = [];
        foreach (I18n::enabledLocales() as $code => $meta) {
            $isDefault = ($code === 'id');
            $localeTabs[] = Tabs\Tab::make("{$meta['flag']} {$meta['label']}")
                ->schema([
                    FileUpload::make("desktop_image.{$code}")
                        ->label("Gambar Desktop ({$meta['short']})")
                        ->helperText($isDefault ? 'Ukuran rekomendasi: 1831 × 859 px (~2.13:1).' : 'Ukuran: 1831 × 859 px. Kosongkan jika ingin menyamakan dengan Bahasa Indonesia.')
                        ->image()
                        ->required($isDefault)
                        ->directory('sliders')
                        ->imageEditor()
                        ->maxSize(6144),
                    FileUpload::make("mobile_image.{$code}")
                        ->label("Gambar Mobile ({$meta['short']})")
                        ->helperText('Ukuran rekomendasi: 1122 × 1402 px (portrait 4:5). Kosongkan jika ingin memakai versi desktop.')
                        ->image()
                        ->directory('sliders')
                        ->imageEditor()
                        ->maxSize(6144),
                ])
                ->columns(2);
        }

        return $form->schema([
            Section::make('Gambar Banner')
                ->description('Desktop 1831 × 859 px · Mobile 1122 × 1402 px. Upload gambar banner sesuai bahasa pilihan pengunjung.')
                ->schema([
                    Tabs::make('Bahasa Banner')->tabs($localeTabs),
                ]),

            Section::make('Teks di Atas Banner')
                ->description('Matikan jika teks sudah menyatu di dalam gambar banner. Judul tetap dipakai untuk label tab dan alt gambar.')
                ->schema([
                    Toggle::make('show_text')->label('Tampilkan teks di banner')->default(false)->live(),
                    Bilingual::text('title', 'Judul / Label Tab'),
                    Bilingual::text('badge', 'Label kecil')->visible(fn (Get $get) => $get('show_text')),
                    Bilingual::textarea('subtitle', 'Sub judul', rows: 2)->visible(fn (Get $get) => $get('show_text')),
                    ToggleButtons::make('text_position')->label('Posisi teks')->inline()->default('left')
                        ->options(['left' => 'Kiri', 'center' => 'Tengah', 'right' => 'Kanan'])
                        ->visible(fn (Get $get) => $get('show_text')),
                ]),

            Section::make('Tombol')
                ->columns(1)
                ->schema([
                    Bilingual::text('cta_text', 'Tombol utama'),
                    TextInput::make('cta_url')->label('Link tombol utama')->placeholder('/id/katalog atau https://...'),
                    Bilingual::text('cta2_text', 'Tombol kedua'),
                    TextInput::make('cta2_url')->label('Link tombol kedua'),
                ])->collapsible(),

            Section::make('Tampilan')
                ->columns(3)
                ->schema([
                    TextInput::make('overlay')->label('Gelap overlay (%)')->numeric()->minValue(0)->maxValue(90)->default(0)
                        ->helperText('Supaya teks terbaca di atas foto terang. 0 = tanpa overlay.'),
                    TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                    Toggle::make('is_active')->label('Aktif')->default(true)->inline(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('desktop_image')
                    ->label('Desktop')
                    ->state(fn (Slider $record) => $record->t('desktop_image', 'id'))
                    ->height(56)->width(110),
                Tables\Columns\ImageColumn::make('mobile_image')
                    ->label('Mobile')
                    ->state(fn (Slider $record) => $record->t('mobile_image', 'id'))
                    ->height(56)->width(45),
                Tables\Columns\TextColumn::make('title.id')->label('Judul')->placeholder('— (teks di gambar)')->limit(40),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
