<?php
namespace App\Repositories\Expense;

use App\Repositories\RepositoryInterface;

interface ExpenseRepositoryInterface extends RepositoryInterface
{
    public function getModel();  // Khai báo phương thức paginate
}
