<?php

use App\Http\Controllers\Admin\CriterionController;
use App\Http\Controllers\Admin\InfluencerController;
use App\Http\Controllers\Admin\InfluencerImportController;
use App\Http\Controllers\Admin\SubCriterionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SawController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = request()->user();

    return match ($user->role) {
        User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
        User::ROLE_MANAJER => redirect()->route('manager.dashboard'),
        default => abort(403),
    };
})->middleware(['auth', 'active'])->name('dashboard');

Route::middleware(['auth', 'active'])->group(function () {
    Route::view('/admin/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/manager/dashboard', 'manager.dashboard')->name('manager.dashboard');
    Route::get('/admin/saw', [SawController::class, 'index'])->middleware('admin')->name('admin.saw.index');
    Route::get('/manager/saw', [SawController::class, 'index'])->name('manager.saw.index');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::resource('criteria', CriterionController::class)->except(['show']);
        Route::get('criteria/{criterion}/sub-criteria', [SubCriterionController::class, 'edit'])->name('criteria.sub-criteria.edit');
        Route::put('criteria/{criterion}/sub-criteria', [SubCriterionController::class, 'update'])->name('criteria.sub-criteria.update');
        Route::resource('influencers', InfluencerController::class)->except(['show']);
        Route::get('influencers-import', [InfluencerImportController::class, 'create'])->name('influencers.import.create');
        Route::get('influencers-import/template', [InfluencerImportController::class, 'template'])->name('influencers.import.template');
        Route::post('influencers-import/preview', [InfluencerImportController::class, 'preview'])->name('influencers.import.preview');
        Route::post('influencers-import', [InfluencerImportController::class, 'store'])->name('influencers.import.store');
    });
});

require __DIR__.'/auth.php';
