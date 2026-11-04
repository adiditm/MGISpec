<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages;
use App\Models\Certificate;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Katalog';

    protected static ?string $modelLabel = 'Sertifikat';

    protected static ?string $pluralModelLabel = 'Sertifikat';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Sertifikat')
                ->description('Tampil sebagai slider kecil di bawah detail setiap produk. Klik gambar akan membuka tampilan besar dengan tombol unduh PDF.')
                ->schema([
                    TextInput::make('title')->label('Nama sertifikat (opsional)')->placeholder('US FDA, Halal, ISO ...')->maxLength(150),
                    FileUpload::make('image')->label('Gambar sertifikat (tampil di slider)')
                        ->helperText('JPG / PNG / WebP, maksimal 4 MB.')
                        ->image()->required()->directory('certificates')->imageEditor()->maxSize(4096),
                    FileUpload::make('pdf_file')->label('File PDF (diunduh pengunjung)')
                        ->helperText('Format PDF, maksimal 10 MB.')
                        ->acceptedFileTypes(['application/pdf'])->required()->directory('certificates')
                        ->downloadable()->maxSize(10240),
                ]),
            Section::make()->columns(2)->schema([
                TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                Toggle::make('is_active')->label('Tampil di website')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Gambar')->height(56),
                Tables\Columns\TextColumn::make('title')->label('Nama')->placeholder('—'),
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
            'index' => Pages\ListCertificates::route('/'),
            'create' => Pages\CreateCertificate::route('/create'),
            'edit' => Pages\EditCertificate::route('/{record}/edit'),
        ];
    }
}
