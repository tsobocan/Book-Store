<?php

    namespace App\Models\Models;

    use App\Models\BookOption;
    use Database\Factories\BookFactory;
    use Illuminate\Database\Eloquent\Relations\HasMany;
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

        public function allActions(): HasMany
        {
            return $this->hasMany(BookAction::class, 'book_id');
        }

        public function bookOptions(): HasMany
        {
            return $this->hasMany(BookOption::class, 'book_id');
        }
    }
