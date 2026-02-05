<?php

use App\Livewire\Dashboard;
use App\Livewire\Groups\Create as GroupsCreate;
use App\Livewire\Groups\Index as GroupsIndex;
use App\Livewire\Groups\ManageMembers;
use App\Livewire\Groups\Show as GroupsShow;
use App\Livewire\Expenses\Create as ExpensesCreate;
use App\Livewire\Expenses\Edit as ExpensesEdit;
use App\Livewire\Expenses\Show as ExpensesShow;
use App\Livewire\Balances\Index as BalancesIndex;
use App\Livewire\Settlements\Create as SettlementsCreate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Groups
    Route::get('/groups', GroupsIndex::class)->name('groups.index');
    Route::get('/groups/create', GroupsCreate::class)->name('groups.create');
    Route::get('/groups/{group}', GroupsShow::class)->name('groups.show');
    Route::get('/groups/{group}/members', ManageMembers::class)->name('groups.members');

    // Expenses (nested under groups)
    Route::get('/groups/{group}/expenses/create', ExpensesCreate::class)->name('expenses.create');
    Route::get('/expenses/{expense}', ExpensesShow::class)->name('expenses.show');
    Route::get('/expenses/{expense}/edit', ExpensesEdit::class)->name('expenses.edit');

    // Balances (nested under groups)
    Route::get('/groups/{group}/balances', BalancesIndex::class)->name('balances.index');

    // Settlements (nested under groups)
    Route::get('/groups/{group}/settlements/create', SettlementsCreate::class)->name('settlements.create');
});

require __DIR__.'/auth.php';
