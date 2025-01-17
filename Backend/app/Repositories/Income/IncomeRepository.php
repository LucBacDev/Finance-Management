<?php
namespace App\Repositories\Income;

use App\Repositories\BaseRepository;

class IncomeRepository extends BaseRepository implements IncomeRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Income::class;
    }


}
