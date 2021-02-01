<?php


    namespace App\Logic;


    use Ramsey\Uuid\Uuid;
    use App\Models\BookOption;
    use App\Models\Models\Book;
    use App\Models\Models\BookAction;

    class BookHelper
    {

        public function takenBooks($id, $dateFrom, $dateTo)
        {
            return BookAction::query()->where('book_id', $id)->where([
                ['valid_from', '<=', $dateTo],
                ['valid_to', '>=', $dateFrom],
            ])->with('actualBook')->whereHas('currentStatus', function ($query) {
                $query->where('title', '!=', 'Completed');
            })->get();
        }

        public function syncBookInstances(Book $book, $newQuantity, $oldQuantity){
            if ($newQuantity < $oldQuantity) {
                $toRemove = BookOption::query()->where('book_id',
                    $book->id)->limit($oldQuantity - $newQuantity)->get();

                BookAction::query()->whereIn('option_id', $toRemove->pluck('id'))->delete();
                $toRemove->each->delete();
            } elseif ($newQuantity > $oldQuantity) {
                do {
                    BookOption::query()->create(['book_id' => $book->id, 'title' => Uuid::uuid4()->toString()]);
                    $oldQuantity = $oldQuantity + 1;
                } while ($oldQuantity != $newQuantity);
            }
        }
    }