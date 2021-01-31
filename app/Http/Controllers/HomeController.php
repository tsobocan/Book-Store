<?php

    namespace App\Http\Controllers;

    use App\Models\Models\Book;
    use Illuminate\Http\Request;

    class HomeController extends Controller
    {
        protected function books()
        {
            return Book::with('author')->get();
        }
    }
