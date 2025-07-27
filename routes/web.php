<?php

use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProgressBarSettingController;
use App\Http\Controllers\ProgressBarStyleController;
use App\Http\Controllers\RewardVariantController;
use App\Http\Controllers\ThresholdController;
use Illuminate\Support\Facades\Route;


Route::middleware(['verify.shopify'])->group(function () {
    Route::get('/', [ProgressBarSettingController::class, 'index'])->name('home');

    Route::put('/progress-bar-settings/update', [ProgressBarSettingController::class, 'update'])
        ->name('progress-bar-settings.update');

    Route::post('/progress-bar/styles/save-bar', [ProgressBarStyleController::class, 'saveBarStyle']);
    Route::post('/progress-bar/styles/save-widget', [ProgressBarStyleController::class, 'saveWidgetStyle']);
    Route::post('/progress-bar/styles/save-drawer', [ProgressBarStyleController::class, 'saveDrawerStyle']);

    Route::get('/thresholds', [ThresholdController::class, 'index'])->name('thresholds.index');
    Route::get('/thresholds/create', [ThresholdController::class, 'create'])->name('thresholds.create');
    Route::post('/thresholds', [ThresholdController::class, 'store'])->name('thresholds.store');
    Route::get('/thresholds/{threshold}/edit', [ThresholdController::class, 'edit'])->name('thresholds.edit');
    Route::post('/thresholds/{threshold}', [ThresholdController::class, 'update'])->name('thresholds.update');
    Route::delete('/thresholds/{threshold}', [ThresholdController::class, 'destroy'])->name('thresholds.destroy');
    Route::get('/search-products', [ThresholdController::class, 'searchProducts'])->name('rules.searchProducts');

    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
});
Route::get('/storefront/settings', [ProgressBarSettingController::class, 'settings']);
Route::post('/storefront/create-reward-variant', [RewardVariantController::class, 'createRewardVariant']);

