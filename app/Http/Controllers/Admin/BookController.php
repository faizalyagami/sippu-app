<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\BookCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('author', 'like', "%{$request->search}%")
                  ->orWhere('isbn', 'like', "%{$request->search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            if ($request->availability == 'available') {
                $query->where('available_stock', '>', 0);
            } elseif ($request->availability == 'unavailable') {
                $query->where('available_stock', '<=', 0);
            }
        }

        $books = $query->orderBy('title')->paginate(10);
        $categories = Category::all();
        
        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1900|max:' . date('Y'),
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'pages' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'location_rack' => 'nullable|string|max:50',
            'total_stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->except('cover_image');
            $data['available_stock'] = $request->total_stock;
            
            if ($request->hasFile('cover_image')) {
                $data['cover_image'] = $request->file('cover_image')->store('books', 'public');
            }

            $book = Book::create($data);

            // Buat book conditions untuk setiap copy
            for ($i = 1; $i <= $book->total_stock; $i++) {
                BookCondition::create([
                    'book_id' => $book->id,
                    'book_code' => $book->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'condition' => 'good',
                    'last_check_date' => now(),
                    'checked_by' => auth()->id(),
                    'is_available' => true
                ]);
            }

            DB::commit();
            return redirect()->route('admin.books.index')->with('success', 'Buku berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        $categories = Category::all();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn,' . $id,
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1900|max:' . date('Y'),
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'pages' => 'nullable|integer|min:1',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'location_rack' => 'nullable|string|max:50',
            'total_stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $request->except('cover_image');
            
            // Handle stock changes
            $stockDiff = $request->total_stock - $book->total_stock;
            $data['available_stock'] = $book->available_stock + $stockDiff;

            if ($request->hasFile('cover_image')) {
                if ($book->cover_image) {
                    Storage::disk('public')->delete($book->cover_image);
                }
                $data['cover_image'] = $request->file('cover_image')->store('books', 'public');
            }

            $book->update($data);

            // Handle additional book conditions for new stock
            if ($stockDiff > 0) {
                $currentCount = $book->bookConditions()->count();
                for ($i = 1; $i <= $stockDiff; $i++) {
                    BookCondition::create([
                        'book_id' => $book->id,
                        'book_code' => $book->id . '-' . str_pad($currentCount + $i, 3, '0', STR_PAD_LEFT),
                        'condition' => 'good',
                        'last_check_date' => now(),
                        'checked_by' => auth()->id(),
                        'is_available' => true
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.books.index')->with('success', 'Buku berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        
        if ($book->borrowed_stock > 0) {
            return redirect()->back()->with('error', 'Buku tidak bisa dihapus karena masih dipinjam');
        }

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil dihapus');
    }
}