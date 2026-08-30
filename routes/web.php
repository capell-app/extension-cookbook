<?php

declare(strict_types=1);

use Capell\ExtensionCookbook\Http\Controllers\ExtensionCookbookController;
use Illuminate\Support\Facades\Route;

Route::get('/extension-cookbook', ExtensionCookbookController::class)->name('capell-extension-cookbook.index');
