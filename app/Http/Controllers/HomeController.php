<?php

    namespace App\Http\Controllers;

    use Exception;
    use Carbon\Carbon;
    use App\Models\User;
    use Ramsey\Uuid\Uuid;
    use App\Logic\BookHelper;
    use App\Models\BookOption;
    use App\Models\Models\Book;
    use Illuminate\Http\Request;
    use App\Models\Models\Status;
    use App\Models\Models\Author;
    use App\Models\Models\BookAction;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Validation\ValidationException;

    class HomeController extends Controller
    {
        /**
         * Returns all books with conditions based on URL parameters.
         * @param Request $request
         * @return JsonResponse
         */
        protected function books(Request $request): JsonResponse
        {
            $books = Book::query()->with('author');

            if ($request->filled('title')) {
                $books->where('title', 'LIKE', '%' . $request->get('title') . '%');
            }
            if ($request->filled('year')) {
                $books->where('year', $request->get('year'));
            }
            if ($request->filled('author')) {
                $books->whereHas('author', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->get('author') . '%');
                });
            }

            return response()->json($books->get());
        }

        /**
         * Returns all reserved and rented action(books).
         * Code -> this is actually unique identifier of a single book in this case.
         * @return JsonResponse
         */
        protected function activeBooks(): JsonResponse
        {
            if(!auth()->user()->hasRole('Admin')){
                return response()->json();
            }

            return response()->json(BookAction::with([
                'book.author',
                'user',
                'actualBook',
                'currentStatus'
            ])->whereHas('currentStatus', function ($query) {
                $query->where('title', '!=', 'Completed');
            })->get());
        }

        /**
         * Adds action on a book. Automatically picks one single unit of a book.
         * @param Request $request
         * @throws ValidationException
         */
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

                $taken = (new BookHelper())->takenBooks($book->id,$dateFrom, $dateTo);

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

        /**
         * Returns users matching query;
         * @param Request $request
         * @return Builder[]|Collection|JsonResponse
         */
        protected function fetchUsers(Request $request)
        {
            if(!auth()->user()->hasRole('Admin')){
                return response()->json();
            }
            return User::query()->where('name', 'LIKE', '%' . $request->get('query') . '%')->get();
        }

        /**
         * Changes status to "Rented".
         * @param Request $request
         */
        protected function rent(Request $request)
        {
            $status = Status::query()->where('title', 'LIKE', 'Rented')->first();
            if ($status) {
                BookAction::query()->where('id', $request->get('id'))->update([
                    'status_id' => $status->id
                ]);
            }
        }

        /**
         * Removes action from a list. Valid for returning/canceling.
         * @param Request $request
         */
        protected function returnBook(Request $request)
        {
            BookAction::query()->where('id', $request->get('id'))->delete();
        }

        /**
         * Removes book from database.
         * @param Request $request
         * @throws Exception
         */
        protected function deleteBook(Request $request)
        {
            $book = Book::query()->where('id', $request->get('id'))->first();
            if ($book) {
                BookAction::query()->where('book_id', $request->get('id'))->delete();
                BookOption::query()->where('book_id', $request->get('id'))->delete();
                $book->delete();
            }
        }

        /**
         * Adds or updates book. Syncs all units of a book.
         * @param Request $request
         * @throws ValidationException
         */
        protected function editBook(Request $request)
        {
            $input = $request->all();
            $book = null;
            $status = null;
            $user = null;

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
            } else {
                $oldQuantity = 0;
                $book = Book::query()->create([
                    'author_id' => $author->id,
                    'title' => $request->get('title'),
                    'year' => $request->get('year'),
                    'quantity' => $newQuantity,
                ]);
            }
            (new BookHelper())->syncBookInstances($book, $newQuantity, $oldQuantity);
        }
    }
