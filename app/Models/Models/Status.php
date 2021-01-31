<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    const VALID_OPTIONS = ['rent' => 'Reserved', 'reserve' => 'Rented'];
}
