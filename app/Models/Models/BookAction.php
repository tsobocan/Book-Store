<?php

    namespace App\Models\Models;

    use App\Models\User;
    use App\Models\BookOption;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    class BookAction extends Model
    {
        use HasFactory;
        protected $table = 'actions';
        protected $guarded = [];

        protected $casts = [
            'valid_from' => 'datetime:d.m.Y',
            'valid_to' => 'datetime:d.m.Y',
        ];
        protected $dates = ['valid_from', 'valid_to'];


        public function currentStatus(): HasOne
        {
            return $this->hasOne(Status::class, 'id', 'status_id');
        }

        public function actualBook(): BelongsTo
        {
            return $this->belongsTo(BookOption::class, 'option_id');
        }

        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class, 'user_id');
        }

        public function book(): BelongsTo
        {
            return $this->belongsTo(Book::class, 'book_id');
        }
    }
