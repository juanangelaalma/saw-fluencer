<?php

use App\Http\Controllers\Admin\CriterionController;
use App\Http\Controllers\Admin\InfluencerController;
use App\Http\Controllers\Admin\InfluencerImportController;
use App\Http\Controllers\Admin\SubCriterionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SawController;
use App\Http\Controllers\SawHistoryController;
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
        User::ROLE_KASI => redirect()->route('kasi.dashboard'),
        default => abort(403),
    };
})->middleware(['auth', 'active'])->name('dashboard');

Route::middleware(['auth', 'active'])->group(function () {
    Route::view('/admin/dashboard', 'admin.dashboard')->middleware('admin')->name('admin.dashboard');
    Route::view('/manager/dashboard', 'manager.dashboard')->name('manager.dashboard');
    Route::view('/kasi/dashboard', 'kasi.dashboard')->name('kasi.dashboard');
    Route::get('/admin/saw', [SawController::class, 'index'])->middleware('admin')->name('admin.saw.index');
    Route::get('/kasi/saw', [SawController::class, 'index'])->name('kasi.saw.index');
    Route::get('/kasi/influencers', [InfluencerController::class, 'index'])->name('kasi.influencers.index');

    Route::get('/saw/history', [SawHistoryController::class, 'index'])->name('saw.history.index');
    Route::post('/saw/history', [SawHistoryController::class, 'store'])->middleware('admin')->name('saw.history.store');
    Route::get('/saw/history/{calculation}', [SawHistoryController::class, 'show'])->name('saw.history.show');
    Route::delete('/saw/history/{calculation}', [SawHistoryController::class, 'destroy'])->middleware('admin')->name('saw.history.destroy');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::post('influencers/destroy-all', [InfluencerController::class, 'destroyAll'])->name('influencers.destroy-all');
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
