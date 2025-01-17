<?php
namespace App\Repositories\Expense;

use App\Repositories\BaseRepository;

class ExpenseRepository extends BaseRepository implements ExpenseRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Expense::class;
    }

}
