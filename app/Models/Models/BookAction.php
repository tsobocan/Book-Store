<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookAction extends Model
{
    use HasFactory;

    protected $table = 'actions';
    protected $guarded = [];
}
