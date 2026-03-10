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
    
    // Procurements
    Route::resource('procurements', App\Http\Controllers\Admin\ProcurementController::class);
    Route::get('/procurements/{procurement}/receive', [App\Http\Controllers\Admin\ProcurementController::class, 'receive'])->name('procurements.receive');
    Route::post('/procurements/{procurement}/receive', [App\Http\Controllers\Admin\ProcurementController::class, 'processReceive'])->name('procurements.process-receive');
    
    // Borrowings
    Route::resource('borrowings', App\Http\Controllers\Admin\BorrowingController::class);
    Route::post('/borrowings/{borrowing}/approve', [App\Http\Controllers\Admin\BorrowingController::class, 'approve'])->name('borrowings.approve');
    Route::post('/borrowings/{borrowing}/reject', [App\Http\Controllers\Admin\BorrowingController::class, 'reject'])->name('borrowings.reject');
    Route::post('/borrowings/{borrowing}/return', [App\Http\Controllers\Admin\BorrowingController::class, 'processReturn'])->name('borrowings.return');
    
    // Reports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/books', [App\Http\Controllers\Admin\ReportController::class, 'books'])->name('reports.books');
    Route::get('/reports/borrowings', [App\Http\Controllers\Admin\ReportController::class, 'borrowings'])->name('reports.borrowings');
    Route::get('/reports/procurements', [App\Http\Controllers\Admin\ReportController::class, 'procurements'])->name('reports.procurements');
    
    // ==================== USER MANAGEMENT ROUTES (DIPINDAHKAN KE DALAM GROUP ADMIN) ====================
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('index');
        Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
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
    Route::get('/books', [App\Http\Controllers\Kaprodi\BookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}', [App\Http\Controllers\Kaprodi\BookController::class, 'show'])->name('books.show');
    
    // Borrowings
    Route::get('/borrowings/checkout', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'checkout'])->name('borrowings.checkout');
    Route::post('/borrowings/checkout', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'processCheckout'])->name('borrowings.process-checkout');
    Route::get('/borrowings', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('/borrowings/{id}', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'show'])->name('borrowings.show');
    Route::post('/borrowings/{id}/cancel', [App\Http\Controllers\Kaprodi\BorrowingController::class, 'cancel'])->name('borrowings.cancel');
    
    // Requests
    Route::resource('requests', App\Http\Controllers\Kaprodi\RequestController::class);
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
    
    // Books
    Route::get('/books', [App\Http\Controllers\Supplier\BookController::class, 'index'])->name('books.index');
    Route::get('/books/{id}', [App\Http\Controllers\Supplier\BookController::class, 'show'])->name('books.show');
    
    // Invoices
    Route::get('/invoices', [App\Http\Controllers\Supplier\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{id}', [App\Http\Controllers\Supplier\InvoiceController::class, 'show'])->name('invoices.show');
});