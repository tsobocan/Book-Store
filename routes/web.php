<?php

    use Illuminate\Foundation\Application;
    use Illuminate\Support\Facades\Route;
    use Inertia\Inertia;
    use App\Http\Controllers\HomeController;

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    Route::get('/', function () {
        return Inertia::render('Home', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    })->name('home');

    Route::get('/actions', function () {
        return Inertia::render('Actions');
    })->name('actions');

    /*
     * This could also be done using ResourceControllers, but I don't like them.
     */
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('/book/action', [HomeController::class, 'createAction'])->name('action');
        Route::post('/book/save', [HomeController::class, 'editBook'])->name('edit');
        Route::post('/book/delete', [HomeController::class, 'deleteBook'])->name('delete');
        Route::post('/users', [HomeController::class, 'fetchUsers'])->name('users');
        Route::get('/books/active', [HomeController::class, 'activeBooks'])->name('active');
        Route::post('/books/rent', [HomeController::class, 'rent'])->name('rent');
        Route::post('/books/return', [HomeController::class, 'returnBook'])->name('return');
    });




