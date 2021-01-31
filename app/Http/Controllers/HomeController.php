<?php

    namespace App\Http\Controllers;

    use Carbon\Carbon;
    use App\Models\User;
    use App\Models\Models\Book;
    use Illuminate\Http\Request;
    use App\Models\Models\Status;
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

        protected function createAction(Request $request)
        {
            $input = $request->all();
            $book = null;
            $status = null;

            Validator::make($input, [
                'fromDate' => 'date|required',
                'toDate' => 'date|required',
                'book' => 'required',
                'action' => 'required',
            ])->after(function ($validator) use ($input, &$book, &$status) {
                $book = Book::query()->where('id', $input['book']['id'])->first();
                if(!$book){
                    $validator->errors()->add('other', 'Book does not exist.');
                }

                $action = array_key_exists($input['action'], Status::VALID_OPTIONS) ? Status::VALID_OPTIONS[$input['action']] : null;
                $status = Status::query()->where('title', 'LIKE', $action)->first();

                if(!$status){
                    $validator->errors()->add('other', 'No valid action.');
                }

                $dateFrom = Carbon::parse($input['fromDate'])->setHour(12);
                $dateTo = Carbon::parse($input['toDate'])->setHour(12);;

                if(!$dateTo->isAfter($dateFrom)){
                    $validator->errors()->add('other', 'Wrong dates.');
                }

                $taken = BookAction::query()->where('book_id', $book->id)
                    ->where([
                        ['valid_from', '<=', $dateTo],
                        ['valid_to', '>=', $dateFrom],
                    ])
                    ->count();
                if($taken >= $book->quantity){
                    $validator->errors()->add('other', 'All books are taken.');
                }

            })->validate();

            BookAction::query()->create([
                'book_id' => $book->id,
                'status_id' => $status->id,
                'valid_from' => $request->get('fromDate'),
                'valid_to' => $request->get('toDate'),
            ]);
        }
    }
