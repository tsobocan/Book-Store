<?php

    namespace App\Models\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Database\Eloquent\Factories\HasFactory;

    class BookAction extends Model
    {
        use HasFactory;

        protected $table = 'actions';
        protected $guarded = [];

        public function currentStatus(): HasOne
        {
            return $this->hasOne(Status::class, 'id', 'status_id');
        }
    }
