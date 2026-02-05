<?php

use App\Livewire\Balances\Index as BalancesIndex;
use App\Livewire\Dashboard;
use App\Livewire\Expenses\Create as ExpensesCreate;
use App\Livewire\Expenses\Edit as ExpensesEdit;
use App\Livewire\Expenses\Show as ExpensesShow;
use App\Livewire\Groups\Create as GroupsCreate;
use App\Livewire\Groups\Index as GroupsIndex;
use App\Livewire\Groups\ManageMembers;
use App\Livewire\Groups\Show as GroupsShow;
use App\Livewire\Settlements\Create as SettlementsCreate;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to dashboard
Route::redirect('/', '/dashboard')->middleware('auth');

Route::middleware(['auth', 'verified', 'throttle.financial'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Groups Management
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/', GroupsIndex::class)->name('index');
        Route::get('/create', GroupsCreate::class)->name('create');

        // Secure Group Routes (Requires Membership)
        Route::middleware(['group.member'])->prefix('{group}')->whereNumber('group')->group(function () {
            Route::get('/', GroupsShow::class)->name('show');
            Route::get('/members', ManageMembers::class)->name('members');
            
            // Nested Resources
            Route::prefix('expenses')->name('expenses.')->group(function () {
                Route::get('/create', ExpensesCreate::class)->name('create');
            });

            Route::prefix('balances')->name('balances.')->group(function () {
                Route::get('/', BalancesIndex::class)->name('index');
            });

            Route::prefix('settlements')->name('settlements.')->group(function () {
                Route::get('/create', SettlementsCreate::class)->name('create');
            });
        });
    });

    // Individual Expense Management
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::prefix('{expense}')->whereNumber('expense')->group(function () {
            Route::get('/', ExpensesShow::class)->name('show');
            Route::get('/edit', ExpensesEdit::class)->name('edit');
        });
    });
});

require __DIR__.'/auth.php';
