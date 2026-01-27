<?php

use Illuminate\Support\Facades\Route;
use NewSong\BibleVerseFinder\Http\Controllers\BibleVerseController;

/*
|--------------------------------------------------------------------------
| Control Panel Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the ServiceProvider and are prefixed with
| the control panel prefix (/cp by default).
|
*/

Route::name('bible-verses.')
    ->prefix('bible-verses')
    ->middleware('can:access bible verses')
    ->group(function () {
        // Fetch single verse
        Route::post('fetch', [BibleVerseController::class, 'fetch'])->name('fetch');

        // Fetch multiple verses
        Route::post('fetch-multiple', [BibleVerseController::class, 'fetchMultiple'])->name('fetch-multiple');

        // Parse reference string
        Route::post('parse', [BibleVerseController::class, 'parse'])->name('parse');

        // Validate verse reference
        Route::post('validate', [BibleVerseController::class, 'validate'])->name('validate');

        // Get chapter count for a book
        Route::get('chapters/{book}', [BibleVerseController::class, 'getChapters'])->name('chapters');

        // Get verse count for a chapter
        Route::get('verses/{book}/{chapter}', [BibleVerseController::class, 'getVerses'])->name('verses');

        // Get available versions
        Route::get('versions', [BibleVerseController::class, 'getVersions'])->name('versions');
    });
