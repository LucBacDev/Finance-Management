<?php

namespace App\Providers;

use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\IncomeRepositoryInterface;
use App\Repositories\Eloquent\ExpenseRepository;
use App\Repositories\Eloquent\IncomeRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        //
    }
}
