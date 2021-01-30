<?php

    namespace App\Models\Models;

    use Database\Factories\BookFactory;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Book extends Model
    {
        use HasFactory;

        protected $table = 'books';
        protected $guarded = [];

        protected static function newFactory(): BookFactory
        {
            return BookFactory::new();
        }

        public function author(): BelongsTo
        {
            return $this->belongsTo(Author::class);
        }
    }
