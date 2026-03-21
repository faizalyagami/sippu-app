<?php

use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==================== AUTH ROUTES ====================
Auth::routes();

// ==================== HOME REDIRECT ====================
Route::get('/', function () {
    // Redirect ke dashboard berdasarkan role
    $user = Auth::user();
    
    if (!$user) {
        return redirect()->route('login');
    }
    
    if ($user->role_id == 1) { // Admin
        return redirect()->route('admin.dashboard');
    } elseif ($user->role_id == 2) { // Kaprodi
        return redirect()->route('kaprodi.dashboard');
    } elseif ($user->role_id == 3) { // Supplier
        return redirect()->route('supplier.dashboard');
    }
    
    return redirect('/');
})->name('home');

// ==================== GENERAL ROUTES (AFTER LOGIN) ====================
Route::middleware(['auth'])->group(function () {
    // Redirect berdasarkan role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        if ($user->role_id == 1) { // Admin
            return redirect()->route('admin.dashboard');
        } elseif ($user->role_id == 2) { // Kaprodi
            return redirect()->route('kaprodi.dashboard');
        } elseif ($user->role_id == 3) { // Supplier
            return redirect()->route('supplier.dashboard');
        }
        
        return redirect('/');
    })->name('dashboard');
    
    // Profile (general)
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
    
    // Borrowings
    Route::resource('borrowings', App\Http\Controllers\Admin\BorrowingController::class);
    Route::post('/borrowings/{borrowing}/approve', [App\Http\Controllers\Admin\BorrowingController::class, 'approve'])->name('borrowings.approve');
    Route::post('/borrowings/{borrowing}/reject', [App\Http\Controllers\Admin\BorrowingController::class, 'reject'])->name('borrowings.reject');
    Route::post('/borrowings/{borrowing}/mark-borrowed', [App\Http\Controllers\Admin\BorrowingController::class, 'markAsBorrowed'])->name('borrowings.mark-borrowed');
    Route::post('/borrowings/{borrowing}/return', [App\Http\Controllers\Admin\BorrowingController::class, 'processReturn'])->name('borrowings.return');
    
    // Reports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/books', [App\Http\Controllers\Admin\ReportController::class, 'books'])->name('reports.books');
    Route::get('/reports/borrowings', [App\Http\Controllers\Admin\ReportController::class, 'borrowings'])->name('reports.borrowings');
    Route::get('/reports/procurements', [App\Http\Controllers\Admin\ReportController::class, 'procurements'])->name('reports.procurements');
    Route::get('/reports/categories', [App\Http\Controllers\Admin\ReportController::class, 'categories'])->name('reports.categories');
    Route::get('/reports/monthly', [App\Http\Controllers\Admin\ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/users', [App\Http\Controllers\Admin\ReportController::class, 'users'])->name('reports.users');

    Route::get('/dashboard/requests', [App\Http\Controllers\Admin\RequestDashboardController::class, 'index'])->name('dashboard.requests');
    Route::get('/dashboard/requests/statistics', [App\Http\Controllers\Admin\RequestDashboardController::class, 'getStatistics'])->name('dashboard.requests.statistics');
    Route::get('/dashboard/requests/faculty-stats', [App\Http\Controllers\Admin\RequestDashboardController::class, 'facultyStats'])->name('dashboard.requests.faculty-stats');

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
        
        // Khusus Kaprodi
        Route::get('/kaprodi', [App\Http\Controllers\Admin\UserController::class, 'kaprodi'])->name('kaprodi');
        
        // Export dan Bulk Actions
        Route::get('/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('export');
        Route::post('/bulk-action', [App\Http\Controllers\Admin\UserController::class, 'bulkAction'])->name('bulk-action');
    });
});

// ==================== KAPRODI ROUTES ====================
Route::prefix('kaprodi')->name('kaprodi.')->middleware(['auth', 'role:kaprodi'])->group(function () {
    // Dashboard Kaprodi
    Route::get('/dashboard', [App\Http\Controllers\Kaprodi\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Kaprodi
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');
    
    // Books
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [App\Http\Controllers\Kaprodi\BookController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Kaprodi\BookController::class, 'show'])->name('show');
        Route::get('/search/ajax', [App\Http\Controllers\Kaprodi\BookController::class, 'search'])->name('search');
    });
    
    // Borrowings (Peminjaman)
    Route::prefix('borrowings')->name('borrowings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'index'])->name('index');
        Route::get('/checkout', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'processCheckout'])->name('process-checkout');
        Route::get('/{id}', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'show'])->name('show');
        Route::post('/{id}/cancel', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'cancel'])->name('cancel');
        Route::post('/cart-data', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'getCartData'])->name('cart-data');
    });
    
    // Requests (Request Buku)
    Route::resource('requests', App\Http\Controllers\Kaprodi\RequestController::class);
    Route::post('/requests/{id}/cancel', [App\Http\Controllers\Kaprodi\RequestController::class, 'cancel'])->name('requests.cancel');
    Route::get('/requests/check-duplicate', [App\Http\Controllers\Kaprodi\RequestController::class, 'checkDuplicate'])->name('requests.check-duplicate');
});

// ==================== SUPPLIER ROUTES ====================
Route::prefix('supplier')->name('supplier.')->middleware(['auth', 'role:supplier'])->group(function () {
    // Dashboard Supplier
    Route::get('/dashboard', [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Supplier
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [App\Http\Controllers\ProfileController::class, 'updatePhoto'])->name('profile.photo');
    
    // Procurements
    Route::get('/procurements', [App\Http\Controllers\Supplier\ProcurementController::class, 'index'])->name('procurements.index');
    Route::get('/procurements/{id}', [App\Http\Controllers\Supplier\ProcurementController::class, 'show'])->name('procurements.show');
    Route::post('/procurements/{id}/confirm', [App\Http\Controllers\Supplier\ProcurementController::class, 'confirm'])->name('procurements.confirm');
    Route::post('/procurements/{id}/ship', [App\Http\Controllers\Supplier\ProcurementController::class, 'ship'])->name('procurements.ship');
    Route::get('/procurements/{id}/shipping-form', [App\Http\Controllers\Supplier\ProcurementController::class, 'getShippingForm'])->name('shipping-form');
    
    // Books
    Route::get('/books', [App\Http\Controllers\Supplier\BookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}', [App\Http\Controllers\Supplier\BookController::class, 'show'])->name('books.show');
    Route::get('/catalog/supplied', [App\Http\Controllers\Supplier\BookController::class, 'catalog'])->name('catalog');
    
    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [App\Http\Controllers\Supplier\InvoiceController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Supplier\InvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/download', [App\Http\Controllers\Supplier\InvoiceController::class, 'download'])->name('download');
        Route::get('/{id}/print', [App\Http\Controllers\Supplier\InvoiceController::class, 'print'])->name('print');
        Route::get('/summary/yearly', [App\Http\Controllers\Supplier\InvoiceController::class, 'summary'])->name('summary');
    });
});