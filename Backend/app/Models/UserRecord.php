<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'income_id',
        'expense_id',
        'category_id',
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với Income
    public function income()
    {
        return $this->belongsTo(Income::class);
    }

    // Quan hệ với Expense
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    // Quan hệ với Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
