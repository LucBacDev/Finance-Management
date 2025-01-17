<?php
namespace App\Repositories\Reminder;

use App\Repositories\BaseRepository;

class ReminderRepository extends BaseRepository implements ReminderRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return \App\Models\Reminder::class;
    }


}
