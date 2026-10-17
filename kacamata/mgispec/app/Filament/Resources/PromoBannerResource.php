<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoBannerResource\Pages;
use App\Filament\Support\Bilingual;
use App\Models\PromoBanner;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PromoBannerResource extends Resource
{
    protected static ?string $model = PromoBanner::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Beranda';

    protected static ?string $modelLabel = 'Material & Teknologi';

    protected static ?string $pluralModelLabel = 'Material Canggih (10 Elemen)';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Gambar')
                ->description('Disarankan 800 × 800 px (kotak). Jika diisi, kartu tampil sebagai gambar penuh. Jika kosong, kartu tampil sebagai desain gradasi dengan ikon & teks di bawah.')
                ->schema([
                    FileUpload::make('image')->label('Gambar kartu')->image()->directory('promos')->imageEditor()->maxSize(4096),
                ]),
            Section::make('Teks')
                ->schema([
                    TextInput::make('badge')->label('Badge')->placeholder('FREE, CERTIFIED, BONUS ...')->maxLength(50),
                    Bilingual::text('title', 'Judul'),
                    Bilingual::textarea('description', 'Deskripsi', rows: 2),
                    TextInput::make('icon')->label('Ikon (Font Awesome)')->placeholder('fa-solid fa-glasses')
                        ->helperText('Cari nama ikon di fontawesome.com/icons'),
                    TextInput::make('link_url')->label('Link (opsional)'),
                ]),
            Section::make()->columns(2)->schema([
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
                Tables\Columns\ImageColumn::make('image')->label('Gambar')->square()->height(56),
                Tables\Columns\TextColumn::make('badge'),
                Tables\Columns\TextColumn::make('title.id')->label('Judul'),
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
            'index' => Pages\ListPromoBanners::route('/'),
            'create' => Pages\CreatePromoBanner::route('/create'),
            'edit' => Pages\EditPromoBanner::route('/{record}/edit'),
        ];
    }
}
