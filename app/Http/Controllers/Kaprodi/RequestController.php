<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:kaprodi');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = BookRequest::with(['category', 'reviewedBy', 'approvedBy'])
            ->where('user_id', $user->id);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistik
        $statistics = [
            'total' => BookRequest::where('user_id', $user->id)->count(),
            'pending' => BookRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => BookRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => BookRequest::where('user_id', $user->id)->where('status', 'rejected')->count(),
            'procured' => BookRequest::where('user_id', $user->id)->where('status', 'procured')->count()
        ];

        return view('kaprodi.requests.index', compact('requests', 'statistics'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('kaprodi.requests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'edition' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'quantity_requested' => 'required|integer|min:1|max:10',
            'reason' => 'required|string|max:1000',
            'specifications' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_budget' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';

        BookRequest::create($data);

        return redirect()->route('kaprodi.requests.index')
            ->with('success', 'Permintaan buku berhasil diajukan.');
    }

    public function show($id)
    {
        $request = BookRequest::with(['category', 'reviewedBy', 'approvedBy'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('kaprodi.requests.show', compact('request'));
    }

    public function edit($id)
    {
        $request = BookRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $categories = Category::where('is_active', true)->get();

        return view('kaprodi.requests.edit', compact('request', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $bookRequest = BookRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'book_title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'edition' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'quantity_requested' => 'required|integer|min:1|max:10',
            'reason' => 'required|string|max:1000',
            'specifications' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_budget' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $bookRequest->update($request->all());

        return redirect()->route('kaprodi.requests.show', $id)
            ->with('success', 'Permintaan buku berhasil diperbarui.');
    }

    public function cancel($id)
    {
        $bookRequest = BookRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $bookRequest->status = 'cancelled';
        $bookRequest->save();

        return redirect()->route('kaprodi.requests.index')
            ->with('success', 'Permintaan buku berhasil dibatalkan.');
    }

    public function checkDuplicate(Request $request)
    {
        $title = $request->get('title');
        $author = $request->get('author');

        $existing = BookRequest::where('title', 'like', "%{$title}%")
            ->when($author, function($q) use ($author) {
                $q->where('author', 'like', "%{$author}%");
            })
            ->exists();

        return response()->json(['exists' => $existing]);
    }
}