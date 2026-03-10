<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:kaprodi');
    }

    public function index(Request $request)
    {
        $query = Book::with('category')->where('is_active', true);

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('author', 'like', "%{$request->search}%")
                  ->orWhere('isbn', 'like', "%{$request->search}%")
                  ->orWhere('publisher', 'like', "%{$request->search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter availability
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('available_stock', '>', 0);
            }
        }

        // Sort
        $sortField = $request->get('sort', 'title');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $books = $query->paginate(12); // Grid view, 12 per page
        
        $categories = Category::where('is_active', true)->get();
        
        // Get unique authors for filter
        $authors = Book::where('is_active', true)
            ->select('author')
            ->distinct()
            ->orderBy('author')
            ->limit(20)
            ->get();

        return view('kaprodi.books.index', compact('books', 'categories', 'authors'));
    }

    public function show($id)
    {
        $book = Book::with(['category', 'bookReviews.user', 'bookConditions' => function($q) {
            $q->where('is_available', true);
        }])->findOrFail($id);

        // Cek apakah user sedang meminjam buku ini
        $user = auth()->user();
        $currentBorrowing = $user->borrowings()
            ->whereIn('status', ['pending', 'approved', 'borrowed'])
            ->whereHas('items', function($q) use ($book) {
                $q->where('book_id', $book->id);
            })
            ->first();

        // Cek apakah user sudah pernah meminjam buku ini sebelumnya
        $hasBorrowed = $user->borrowings()
            ->where('status', 'returned')
            ->whereHas('items', function($q) use ($book) {
                $q->where('book_id', $book->id);
            })
            ->exists();

        // Rekomendasi buku serupa (kategori sama)
        $recommendations = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('is_active', true)
            ->where('available_stock', '>', 0)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('kaprodi.books.show', compact(
            'book', 
            'currentBorrowing', 
            'hasBorrowed',
            'recommendations'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $books = Book::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('author', 'like', "%{$query}%")
                  ->orWhere('isbn', 'like', "%{$query}%");
            })
            ->where('available_stock', '>', 0)
            ->limit(10)
            ->get(['id', 'title', 'author', 'isbn', 'available_stock']);

        return response()->json($books);
    }
}