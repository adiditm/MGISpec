<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Throwable;

/**
 * Runs `php artisan migrate --force` from the CMS, because the shared hosting has no terminal.
 * Only logged-in admins can reach it, and it runs nothing but the migrations in database/migrations.
 */
class DatabaseMigrations extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Migrasi Database';

    protected static ?string $title = 'Migrasi Database';

    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.database-migrations';

    public string $output = '';

    /** Migration names in database/migrations that have not run yet. */
    public function getPending(): array
    {
        $migrator = app('migrator');
        $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));
        $ran = $migrator->repositoryExists() ? $migrator->getRepository()->getRan() : [];

        return array_values(array_diff($files, $ran));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('migrate')
                ->label('Jalankan migrasi')
                ->icon('heroicon-o-play')
                ->requiresConfirmation()
                ->modalDescription('Menjalankan semua migrasi yang belum jalan (php artisan migrate --force). Lanjutkan?')
                ->disabled(fn () => $this->getPending() === [])
                ->action(function () {
                    try {
                        Artisan::call('migrate', ['--force' => true]);
                        $this->output = trim(Artisan::output());
                        Notification::make()->title('Migrasi selesai')->success()->send();
                    } catch (Throwable $e) {
                        $this->output = $e->getMessage();
                        Notification::make()->title('Migrasi gagal')->body($e->getMessage())->danger()->send();
                    }
                }),
        ];
    }
}
