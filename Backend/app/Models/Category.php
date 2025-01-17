<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'description',
        'type_id',
        'user_id',
    ];

    public function income()
    {
        return $this->hasOne(Income::class);
    }
    public function expense()
    {
        return $this->hasOne(Expense::class);
    }
}

