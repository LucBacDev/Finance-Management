<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomeApiController;
use App\Http\Controllers\ExpenseApiController;
use App\Http\Controllers\CateroryApiConntroller;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\AuthController;

Route::middleware('api')->get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('categories', CateroryApiConntroller::class);
    Route::apiResource('incomes', IncomeApiController::class);
    Route::apiResource('expenses', ExpenseApiController::class);
    Route::apiResource('reminders', ReminderController::class);
    Route::get('reminder-list', [ReminderController::class,'getList']);

    Route::get('income-summary', [StatisticController::class, 'getIncomeSummaryByDate']);
    Route::get('income-total', [StatisticController::class, 'getTotalIncomeByMonthYear']);

    Route::get('expense-summary', [StatisticController::class, 'getExpenseSummaryByDate']);
    Route::get('expense-total', [StatisticController::class, 'getTotalExpenseByMonthYear']);

    Route::get('total-day', [StatisticController::class, 'getTotalDay']);

    Route::get('statistics', [StatisticController::class, 'getStatisticsForPeriod']);

    Route::get('/users', [UserController::class, 'getUser']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/update', [UserController::class, 'update']);
});

// Route::middleware('auth:api')->post('logout', [UserController::class, 'logout']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/password/email', [UserController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);


Route::post('/login/google', [AuthController::class, 'loginWithGoogle']);
