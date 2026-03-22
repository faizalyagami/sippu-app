<?php

use App\Http\Controllers\Admin\BorrowingController;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==================== AUTH ROUTES ====================
Auth::routes();

// ==================== HOME REDIRECT ====================
Route::get('/', function () {
    $user = Auth::user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->role_id == 1) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role_id == 2) {
        return redirect()->route('kaprodi.dashboard');
    } elseif ($user->role_id == 3) {
        return redirect()->route('supplier.dashboard');
    }

    return redirect('/');
})->name('home');

// ==================== GENERAL ROUTES (AFTER LOGIN) ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role_id == 1) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role_id == 2) {
            return redirect()->route('kaprodi.dashboard');
        } elseif ($user->role_id == 3) {
            return redirect()->route('supplier.dashboard');
        }

        return redirect('/');
    })->name('dashboard');

    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/delete/all', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('destroy-all');
        Route::get('/unread/count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });
});

// ==================== ADMIN ROUTES ====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard Admin
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Profile Admin
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Books
    Route::resource('books', App\Http\Controllers\Admin\BookController::class);
    Route::post('/books/{book}/update-stock', [App\Http\Controllers\Admin\BookController::class, 'updateStock'])->name('books.update-stock');
    Route::get('/books/export', [App\Http\Controllers\Admin\BookController::class, 'export'])->name('books.export');

    // Categories
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // Suppliers
    Route::resource('suppliers', App\Http\Controllers\Admin\SupplierController::class);
    Route::post('/suppliers/{id}/toggle-status', [App\Http\Controllers\Admin\SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');

    // Procurements
    Route::resource('procurements', App\Http\Controllers\Admin\ProcurementController::class);
    Route::get('/procurements/{procurement}/receive', [App\Http\Controllers\Admin\ProcurementController::class, 'receive'])->name('procurements.receive');
    Route::post('/procurements/{procurement}/receive', [App\Http\Controllers\Admin\ProcurementController::class, 'processReceive'])->name('procurements.process-receive');
    Route::post('/procurements/{procurement}/cancel', [App\Http\Controllers\Admin\ProcurementController::class, 'cancel'])->name('procurements.cancel');
    Route::get('/procurements/{procurement}/print', [App\Http\Controllers\Admin\ProcurementController::class, 'print'])->name('procurements.print');
    Route::post('/procurements/{procurement}/confirm', [App\Http\Controllers\Admin\ProcurementController::class, 'confirm'])->name('procurements.confirm');

    // Borrowings (Permintaan Koleksi)
    Route::resource('borrowings', App\Http\Controllers\Admin\BorrowingController::class);

    // ROUTE UNTUK ITEM (APPROVE/REJECT PER ITEM) - HANYA SATU KALI
    Route::post('/borrowings/items/{id}/approve', [App\Http\Controllers\Admin\BorrowingController::class, 'approveItem'])->name('borrowings.approve-item');
    Route::post('/borrowings/items/{id}/reject', [App\Http\Controllers\Admin\BorrowingController::class, 'rejectItem'])->name('borrowings.reject-item');

    // ROUTE UNTUK BULK ACTION (OPSIONAL)
    Route::post('/borrowings/{id}/approve-all', [App\Http\Controllers\Admin\BorrowingController::class, 'approveAll'])->name('borrowings.approve-all');
    Route::post('/borrowings/{id}/reject-all', [App\Http\Controllers\Admin\BorrowingController::class, 'rejectAll'])->name('borrowings.reject-all');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/books', [App\Http\Controllers\Admin\ReportController::class, 'books'])->name('books');
        Route::get('/borrowings', [App\Http\Controllers\Admin\ReportController::class, 'borrowings'])->name('borrowings');
        Route::get('/procurements', [App\Http\Controllers\Admin\ReportController::class, 'procurements'])->name('procurements');
        Route::get('/categories', [App\Http\Controllers\Admin\ReportController::class, 'categories'])->name('categories');
        Route::get('/monthly', [App\Http\Controllers\Admin\ReportController::class, 'monthly'])->name('monthly');
        Route::get('/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('users');
    });

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('update');
        Route::post('/{id}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('reset-password');
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('destroy');
        Route::get('/kaprodi', [App\Http\Controllers\Admin\UserController::class, 'kaprodi'])->name('kaprodi');
        Route::get('/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('export');
        Route::post('/bulk-action', [App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('bulk-action');
    });
});

// ==================== KAPRODI ROUTES ====================
Route::prefix('kaprodi')->name('kaprodi.')->middleware(['auth', 'role:kaprodi'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Kaprodi\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Books
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [App\Http\Controllers\Kaprodi\BookController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Kaprodi\BookController::class, 'show'])->name('show');
        Route::get('/search/ajax', [App\Http\Controllers\Kaprodi\BookController::class, 'search'])->name('search');
    });

    // Borrowings (Permintaan)
    Route::prefix('borrowings')->name('borrowings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'index'])->name('index');
        Route::get('/checkout', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'processCheckout'])->name('process-checkout');
        Route::get('/{id}', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'cancel'])->name('cancel');
        Route::post('/cart-data', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'getCartData'])->name('cart-data');

        // Route untuk clear cart session - DIPERBAIKI
        Route::post('/clear-cart-session', function () {
            session()->forget('clear_cart');
            session()->forget('clear_cart_now');
            return response()->json(['success' => true]);
        })->name('clear-cart-session');
    });

    // Requests
    Route::resource('requests', App\Http\Controllers\Kaprodi\RequestController::class);
    Route::post('/requests/{id}/cancel', [App\Http\Controllers\Kaprodi\RequestController::class, 'cancel'])->name('requests.cancel');
    Route::get('/requests/check-duplicate', [App\Http\Controllers\Kaprodi\RequestController::class, 'checkDuplicate'])->name('requests.check-duplicate');
});

// ==================== SUPPLIER ROUTES ====================
Route::prefix('supplier')->name('supplier.')->middleware(['auth', 'role:supplier'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Procurements
    Route::prefix('procurements')->name('procurements.')->group(function () {
        Route::get('/', [App\Http\Controllers\Supplier\ProcurementController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Supplier\ProcurementController::class, 'show'])->name('show');
        Route::post('/{id}/confirm', [App\Http\Controllers\Supplier\ProcurementController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/ship', [App\Http\Controllers\Supplier\ProcurementController::class, 'ship'])->name('ship');
        Route::get('/{id}/shipping-form', [App\Http\Controllers\Supplier\ProcurementController::class, 'getShippingForm'])->name('shipping-form');
    });

    // Books
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [App\Http\Controllers\Supplier\BookController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Supplier\BookController::class, 'show'])->name('show');
        Route::get('/catalog/supplied', [App\Http\Controllers\Supplier\BookController::class, 'catalog'])->name('catalog');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [App\Http\Controllers\Supplier\InvoiceController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Supplier\InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/download', [App\Http\Controllers\Supplier\InvoiceController::class, 'download'])->name('download');
        Route::get('/{id}/print', [App\Http\Controllers\Supplier\InvoiceController::class, 'print'])->name('print');
        Route::get('/summary/yearly', [App\Http\Controllers\Supplier\InvoiceController::class, 'summary'])->name('summary');
    });
});

Route::get('/debug-borrowing/{id}', function ($id) {
    $borrowing = \App\Models\Borrowing::find($id);
    $items = \App\Models\BorrowingItem::where('borrowing_id', $id)->get();

    return response()->json([
        'borrowing' => [
            'id' => $borrowing->id,
            'status' => $borrowing->status,
            'user_id' => $borrowing->user_id
        ],
        'items' => $items->map(function ($item) {
            return [
                'id' => $item->id,
                'book_id' => $item->book_id,
                'quantity' => $item->quantity,
                'status' => $item->status
            ];
        })
    ]);
})->middleware(['auth']);
