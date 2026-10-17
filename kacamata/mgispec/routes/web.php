<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\SetLocale;
use App\Support\I18n;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $locale = $request->cookie('app_locale');

    return redirect('/'.(array_key_exists($locale, I18n::LOCALES) ? $locale : 'id'));
});

Route::prefix('{locale}')
    ->where(['locale' => implode('|', array_keys(I18n::LOCALES))])
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::get('/', HomeController::class)->name('home');
        Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
        Route::get('/katalog/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');
        Route::get('/kontak', [ContactController::class, 'index'])->name('contact');
    });







