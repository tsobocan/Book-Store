<?php

    namespace App\Models\Models;

    use Database\Factories\AuthorFactory;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Author extends Model
    {
        use HasFactory;

        protected $table = 'authors';

        protected $guarded = [];

        protected static function newFactory(): AuthorFactory
        {
            return AuthorFactory::new();
        }

        public function books(): HasMany
        {
            return $this->hasMany(Book::class, 'author_id');
        }
    }
