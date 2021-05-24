<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'item',
        'amount',
        'user_id',
    ];

    public function users() {
        return $this->belongsTo(User::class);
    }
}
