<?php
namespace App\Repositories\Reminder;


use App\Repositories\RepositoryInterface;

interface ReminderRepositoryInterface extends RepositoryInterface
{
    public function getModel();  // Khai báo phương thức paginate
}
