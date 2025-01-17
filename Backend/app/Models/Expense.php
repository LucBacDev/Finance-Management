<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    // Các cột được phép thêm/sửa
    protected $fillable = [
        'user_id',
        'title',
        'amount',
        'category_id',
        'date',
        'description',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
