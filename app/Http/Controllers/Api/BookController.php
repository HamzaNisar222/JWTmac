<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    // List all books
    public function index()
    {
        $user=Auth::user();
        if($user){
        $books = Book::all();
        return BookResource::collection($books);
        }
    }

    // Show a specific book
    public function show($id)
    {
        $book = Book::findOrFail($id);
        return new BookResource($book);
    }

    // Create a new book
    public function store(Request $request)
    {

        $book = Book::create($request->all());

        return new BookResource($book);
    }

    // Update an existing book
    public function update(Request $request, $id)
    {

        $book = Book::findOrFail($id);
        $book->update($request->all());

        return new BookResource($book);
    }

    // Delete a book
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json(['message' => 'Book deleted successfully']);
    }
}
