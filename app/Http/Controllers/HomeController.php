<?php

    namespace App\Http\Controllers;

    use Carbon\Carbon;
    use App\Models\User;
    use Ramsey\Uuid\Uuid;
    use App\Models\BookOption;
    use App\Models\Models\Book;
    use Illuminate\Http\Request;
    use App\Models\Models\Status;
    use App\Models\Models\Author;
    use App\Models\Models\BookAction;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Notifications\Action;
    use Illuminate\Support\Facades\Validator;

    class HomeController extends Controller
    {
        protected function books()
        {
            return Book::with('author')->get();
        }

        protected function activeBooks()
        {
            return BookAction::with(['book.author', 'user', 'actualBook', 'currentStatus'])->whereHas('currentStatus',
                function ($query) {
                    $query->where('title', '!=', 'Completed');
                })->get();
        }

        protected function createAction(Request $request)
        {
            $input = $request->all();
            $book = null;
            $status = null;
            $user = null;
            $taken = collect();
            Validator::make($input, [
                'fromDate' => 'date|required',
                'toDate' => 'date|required',
                'book' => 'required',
                'action' => 'required',
            ])->after(function ($validator) use ($input, &$book, &$status, &$user, &$taken) {
                if (array_key_exists('selected', $input) && is_array($input['selected']) && array_key_exists('id',
                        $input['selected'])) {
                    $user = User::query()->find($input['selected']['id']);
                }

                $book = Book::query()->where('id', $input['book']['id'])->first();
                if (!$book) {
                    $validator->errors()->add('other', 'Book does not exist.');
                }

                $action = array_key_exists($input['action'],
                    Status::VALID_OPTIONS) ? Status::VALID_OPTIONS[$input['action']] : null;
                $status = Status::query()->where('title', 'LIKE', $action)->first();

                if (!$status) {
                    $validator->errors()->add('other', 'No valid action.');
                }

                $dateFrom = Carbon::parse($input['fromDate'])->setHour(12);
                $dateTo = Carbon::parse($input['toDate'])->setHour(12);;

                if (!$dateTo->isAfter($dateFrom)) {
                    $validator->errors()->add('other', 'Wrong dates.');
                }

                $taken = BookAction::query()->where('book_id', $book->id)->where([
                    ['valid_from', '<=', $dateTo],
                    ['valid_to', '>=', $dateFrom],
                ])->with('actualBook')->whereHas('currentStatus', function ($query) {
                    $query->where('title', '!=', 'Completed');
                })->get();
                if ($taken->count() >= $book->quantity) {
                    $validator->errors()->add('other', 'All books are taken.');
                }

            })->validate();

            $ids = $taken->pluck('actualBook')->transform(function ($item) {
                return $item->id;
            });

            $freeBook = BookOption::query()->whereNotIn('id', $ids)->first();

            BookAction::query()->create([
                'user_Id' => $user ? $user->id : auth()->id(),
                'book_id' => $book->id,
                'status_id' => $status->id,
                'option_id' => $freeBook->id,
                'valid_from' => $request->get('fromDate'),
                'valid_to' => $request->get('toDate'),
            ]);
        }

        protected function fetchUsers(Request $request)
        {
            return User::query()->where('name', 'LIKE', '%' . $request->get('query') . '%')->get();
        }

        protected function rent(Request $request)
        {
            $status = Status::query()->where('title', 'LIKE', 'Rented')->first();
            if ($status) {
                BookAction::query()->where('id', $request->get('id'))->update([
                    'status_id' => $status->id
                ]);
            }
        }

        protected function returnBook(Request $request)
        {
            BookAction::query()->where('id', $request->get('id'))->delete();
        }

        protected function editBook(Request $request)
        {
            $input = $request->all();
            $book = null;
            $status = null;
            $user = null;
            $taken = collect();

            Validator::make($input, [
                'title' => 'required|min:2',
                'author' => 'required|min:2',
                'year' => 'required|integer|min:1|max:' . now()->year,
                'quantity' => 'required|min:1',
            ])->validate();

            $author = Author::query()->firstOrCreate(['name' => $request->get('author')]);

            $book = Book::query()->where('id', $request->get('id'))->first();
            $newQuantity = $request->get('quantity');
            if ($book) {
                $oldQuantity = $book->quantity;


                $book->update([
                    'author_id' => $author->id,
                    'title' => $request->get('title'),
                    'year' => $request->get('year'),
                    'quantity' => $newQuantity,
                ]);

                if ($newQuantity < $oldQuantity) {
                    $toRemove = BookOption::query()->where('book_id',
                        $book->id)->limit($oldQuantity - $newQuantity)->get();

                    BookAction::query()->whereIn('option_id', $toRemove->pluck('id'))->delete();
                    $toRemove->each->delete();
                } elseif ($newQuantity > $oldQuantity) {
                    do {
                        BookOption::query()->create(['book_id' => $book->id, 'title' => Uuid::uuid4()->toString()]);
                        $oldQuantity = $oldQuantity + 1;
                    } while ($oldQuantity != $request->get('quantity'));
                }
            } else {
                $oldQuantity = 0;
                    $book = Book::query()->create([
                        'author_id' => $author->id,
                        'title' => $request->get('title'),
                        'year' => $request->get('year'),
                        'quantity' => $newQuantity,
                    ]);

                do {
                    BookOption::query()->create(['book_id' => $book->id, 'title' => Uuid::uuid4()->toString()]);
                    $oldQuantity = $oldQuantity + 1;
                } while ($oldQuantity != $request->get('quantity'));
            }


        }
    }
