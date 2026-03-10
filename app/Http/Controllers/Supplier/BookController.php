<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:supplier');
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

        // Filter by publication year
        if ($request->filled('year')) {
            $query->where('publication_year', $request->year);
        }

        $books = $query->orderBy('title')->paginate(15);

        $categories = Category::where('is_active', true)->get();
        
        // Get available years for filter
        $years = Book::where('is_active', true)
            ->select('publication_year')
            ->distinct()
            ->orderBy('publication_year', 'desc')
            ->get()
            ->pluck('publication_year');

        return view('supplier.books.index', compact('books', 'categories', 'years'));
    }

    public function show($id)
    {
        $book = Book::with(['category', 'procurementItems.procurement' => function($q) {
            $q->where('vendor_id', Auth::user()->supplier->id)
              ->where('status', 'completed')
              ->latest();
        }])->findOrFail($id);

        // Hitung berapa kali supplier ini menyediakan buku ini
        $supplyCount = $book->procurementItems()
            ->whereHas('procurement', function($q) {
                $q->where('vendor_id', Auth::user()->supplier->id)
                  ->where('status', 'completed');
            })
            ->count();

        return view('supplier.books.show', compact('book', 'supplyCount'));
    }

    public function catalog()
    {
        // Buku-buku yang pernah disuplai oleh supplier ini
        $supplier = Auth::user()->supplier;
        
        $suppliedBooks = Book::whereHas('procurementItems.procurement', function($q) use ($supplier) {
            $q->where('vendor_id', $supplier->id)
              ->where('status', 'completed');
        })
        ->withCount(['procurementItems as total_supplied' => function($q) use ($supplier) {
            $q->whereHas('procurement', function($query) use ($supplier) {
                $query->where('vendor_id', $supplier->id);
            });
        }])
        ->orderByDesc('total_supplied')
        ->paginate(15);

        return view('supplier.books.catalog', compact('suppliedBooks'));
    }
}