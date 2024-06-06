<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\Providers\JWT;

class BookController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api')->except(['index', 'show']);
        $this->middleware('Validation:book')->only(['store', 'update']);
    }

    // List all books
    public function index()
    {

        $books = Book::all();
        return BookResource::collection($books);
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
        $requestData = $request->all();
        $user = JWTAuth::parsetoken()->authenticate();
        $requestData['user_id'] = $user->id; // Add the authenticated user's ID to the request data
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('books/images', 'public'); // Save the image file in the 'books/images' directory
            $requestData['image'] = $imagePath; // Add the image path to the request data
        }

        $book = Book::create($requestData); // Create the book record with all request data

        return new BookResource($book);
    }


    public function update(Request $request, $id)
    {
        // Log request data for debugging
        Log::info('Request Data:', $request->all());

        $requestData = $request->all(); // Get all request data
        $user = JWTAuth::parseToken()->authenticate();
        $book = Book::findOrFail($id);

        // Ensure the authenticated user is the owner of the book
        if ($book->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if the image data is included in the update request
        if (!empty($requestData['image']) && !empty($requestData['image']['data'])) {
            // Decode the Base64-encoded image
            $imageData = $requestData['image']['data'];
            $imageName = $requestData['image']['fileName'];
            $imageType = $requestData['image']['fileType'];

            // Ensure the image data is valid
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    return response()->json(['message' => 'Invalid image type'], 400);
                }

                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    return response()->json(['message' => 'Base64 decoding failed'], 400);
                }
            } else {
                return response()->json(['message' => 'Invalid image data'], 400);
            }

            // Delete the previously stored image associated with the book, if it exists
            if (!empty($book->image)) {
                $previousImagePath = storage_path('app/public/' . $book->image);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }

            // Store the new image
            $imagePath = 'books/images/' . uniqid() . '.' . $type;
            Storage::disk('public')->put($imagePath, $imageData);
            $requestData['image'] = $imagePath;
        }

        try {
            // Update the book record with the new data
            $book->update($requestData);

            // Debug logs
            Log::info('Book Updated:', $book->toArray());
        } catch (\Exception $e) {
            Log::error('Update Failed:', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update book', 'error' => $e->getMessage()], 500);
        }

        return new BookResource($book);
    }




    // Delete a book
    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $book = Book::findOrFail($id);
        if ($book->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Delete associated file if it exists
        if (!empty($book->image)) {
            $filePath = public_path($book->image);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete the book record from the database
        $book->delete();

        return response()->json(['message' => 'Book deleted successfully']);
    }
}
