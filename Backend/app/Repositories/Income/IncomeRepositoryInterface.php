<?php
namespace App\Repositories\Income;


use App\Repositories\RepositoryInterface;

interface IncomeRepositoryInterface extends RepositoryInterface
{
    public function getModel();  // Khai báo phương thức paginate
}
