<?php

namespace App\Filament\Resources;

use App\Filament\Forms\IconPicker;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Support\Bilingual;
use App\Models\Product;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationGroup = 'Katalog';

    protected static ?string $modelLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->columns(1)->schema([
            Tabs::make()->persistTabInQueryString()->tabs([
                Tabs\Tab::make('Info Utama')->icon('heroicon-m-information-circle')->schema([
                    Bilingual::text('name', 'Nama produk', required: true),
                    Section::make()->columns(3)->schema([
                        TextInput::make('sku')->label('SKU / Kode')->required()->unique(ignoreRecord: true),
                        TextInput::make('slug')->required()->unique(ignoreRecord: true)
                            ->helperText('URL produk')
                            ->suffixAction(\Filament\Forms\Components\Actions\Action::make('gen')->icon('heroicon-m-arrow-path')
                                ->action(fn (Set $set, Get $get) => $set('slug', Str::slug($get('name.id'))))),
                        Select::make('category_id')->label('Kategori')->relationship('category', 'slug')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['id'] ?? $record->slug)
                            ->preload()->searchable(),
                        TextInput::make('badge')->label('Badge')->placeholder('Best Seller, Hospital Grade ...')->maxLength(50),
                        Select::make('badge_color')->label('Warna badge')->options(Product::BADGE_COLORS)->default('pink'),
                        TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                    ]),
                    Bilingual::textarea('short_description', 'Ringkasan singkat', rows: 3),
                    Section::make()->columns(2)->schema([
                        Toggle::make('is_active')->label('Tampil di website')->default(true),
                        Toggle::make('is_featured')->label('Tampil di carousel Beranda')->default(true),
                    ]),
                ]),

                Tabs\Tab::make('Foto')->icon('heroicon-m-photo')->schema([
                    FileUpload::make('thumbnail')->label('Foto utama / thumbnail')->helperText('Kotak 800 × 800 px, latar putih/bersih.')
                        ->image()->required()->directory('products')->imageEditor()->maxSize(4096),
                    FileUpload::make('gallery')->label('Galeri foto (bisa banyak, drag untuk urutkan)')
                        ->image()->multiple()->reorderable()->appendFiles()->panelLayout('grid')
                        ->directory('products')->maxFiles(12)->maxSize(4096),
                ]),

                Tabs\Tab::make('Deskripsi')->icon('heroicon-m-document-text')->schema([
                    Bilingual::rich('description', 'Deskripsi lengkap'),
                    Repeater::make('highlights')->label('Poin keunggulan (checklist)')
                        ->schema([Bilingual::text('text', 'Poin')])
                        ->itemLabel(fn (array $state) => $state['text']['id'] ?? null)
                        ->collapsible()->reorderable()->defaultItems(0)->addActionLabel('Tambah poin'),
                ]),

                Tabs\Tab::make('Spesifikasi & Teknologi')->icon('heroicon-m-adjustments-horizontal')->schema([
                    Section::make('Spesifikasi dimensi')
                        ->description('Ukuran fisik frame: lebar lensa, bridge, tinggi, panjang kaki, material.')
                        ->schema([
                            Repeater::make('specs')->label('Spesifikasi')
                                ->schema([
                                    Bilingual::text('label', 'Nama spesifikasi'),
                                    TextInput::make('value')->label('Nilai')->placeholder('52 mm, Titanium, 14 g ...'),
                                ])
                                ->itemLabel(fn (array $state) => trim(($state['label']['id'] ?? '').' : '.($state['value'] ?? ''), ' :'))
                                ->collapsible()->reorderable()->defaultItems(0)->addActionLabel('Tambah spesifikasi'),
                        ]),

                    Section::make('Teknologi Frame')
                        ->description('Tampil di tab "Teknologi Frame" pada halaman produk. Maksimal 12 item.')
                        ->schema([
                            Actions::make([
                                Action::make('loadFramePreset')
                                    ->label(fn () => '+ Muat '.count(Product::frameTechnologyPreset()).' Material Standar MGISpec')
                                    ->icon('heroicon-m-sparkles')->color('gray')
                                    ->requiresConfirmation()
                                    ->modalHeading('Muat material standar MGISpec?')
                                    ->modalDescription('Daftar teknologi frame di produk ini akan DIGANTI dengan material standar (sama dengan daftar di Beranda). Lanjutkan?')
                                    ->action(fn (Set $set) => $set('frame_technologies', Product::frameTechnologyPreset())),
                            ]),
                            self::technologyRepeater('frame_technologies', 12),
                        ]),

                    Section::make('Teknologi Lensa')
                        ->description('Tampil di tab "Teknologi Lensa" pada halaman produk. Isi 4, 6, atau 9 item (maksimal 9) supaya kartunya rapi.')
                        ->schema([
                            self::technologyRepeater('lens_technologies', 9),
                        ]),
                ]),
            ]),
        ]);
    }

    /** One card per item: any Font Awesome icon class, a title and a short explanation, in each language. */
    private static function technologyRepeater(string $name, int $max): Repeater
    {
        return Repeater::make($name)->label('Daftar teknologi')
            ->schema([
                IconPicker::make('icon')->label('Ikon')->default('fa-solid fa-atom'),
                Bilingual::text('title', 'Judul'),
                Bilingual::textarea('description', 'Penjelasan', rows: 2),
            ])
            ->itemLabel(fn (array $state) => $state['title']['id'] ?? null)
            ->maxItems($max)
            ->collapsible()->collapsed()->reorderable()->defaultItems(0)->addActionLabel('Tambah teknologi');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')->label('')->square()->height(52),
                Tables\Columns\TextColumn::make('name.id')->label('Nama')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('sku')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('category.name.id')->label('Kategori')->badge(),
                Tables\Columns\TextColumn::make('badge')->toggleable(),
                Tables\Columns\ToggleColumn::make('is_featured')->label('Beranda'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')->label('Kategori')->relationship('category', 'slug'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')->label('Lihat')->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (Product $record) => route('catalog.show', ['locale' => 'id', 'product' => $record]))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
