<?php

namespace App\Providers;

use App\Models\Balance;
use App\Models\Expense;
use App\Models\Group;
use App\Models\Settlement;
use App\Models\User;
use App\Observers\BalanceObserver;
use App\Observers\ExpenseObserver;
use App\Observers\GroupObserver;
use App\Observers\SettlementObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Group::class => \App\Policies\GroupPolicy::class,
        Expense::class => \App\Policies\ExpensePolicy::class,
        Settlement::class => \App\Policies\SettlementPolicy::class,
        Balance::class => \App\Policies\BalancePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Register Observers
        Expense::observe(ExpenseObserver::class);
        Settlement::observe(SettlementObserver::class);
        Group::observe(GroupObserver::class);
        User::observe(UserObserver::class);
        Balance::observe(BalanceObserver::class);

        // Register Policies
        Gate::policy(Group::class, \App\Policies\GroupPolicy::class);
        Gate::policy(Expense::class, \App\Policies\ExpensePolicy::class);
        Gate::policy(Settlement::class, \App\Policies\SettlementPolicy::class);
        Gate::policy(Balance::class, \App\Policies\BalancePolicy::class);
    }
}
