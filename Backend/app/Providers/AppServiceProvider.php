<?php

namespace App\Providers;

use App\Models\Income;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Income\IncomeRepositoryInterface;
use App\Repositories\Income\IncomeRepository;
use App\Repositories\Expense\ExpenseRepository;
use App\Repositories\Expense\ExpenseRepositoryInterface;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Statistic\StatisticRepository;
use App\Repositories\Statistic\StatisticRepositoryInterface;
use App\Repositories\Reminder\ReminderRepository;
use App\Repositories\Reminder\ReminderRepositoryInterface;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IncomeRepositoryInterface::class, IncomeRepository::class);
        $this->app->singleton(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->singleton(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->singleton(StatisticRepositoryInterface::class, StatisticRepository::class);
        $this->app->singleton(ReminderRepositoryInterface::class, ReminderRepository::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
