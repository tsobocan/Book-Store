<?php

namespace App\Models;

use App\Models\Models\Book;
use Database\Factories\BookOptionFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookOption extends Model
{
    use HasFactory;

    protected $table = 'book_options';

    protected $guarded = [];

    public $timestamps = false;

    protected static function newFactory(): BookOptionFactory
    {
        return BookOptionFactory::new();
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'id', 'book_id');
    }
}
