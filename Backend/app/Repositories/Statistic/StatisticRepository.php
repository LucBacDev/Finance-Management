<?php
namespace App\Repositories\Statistic;

use App\Repositories\BaseRepository;

class StatisticRepository extends BaseRepository implements StatisticRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Statistic::class;
    }


}
