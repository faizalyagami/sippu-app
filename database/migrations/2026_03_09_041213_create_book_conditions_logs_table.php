<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookConditionsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_conditions_logs', function (Blueprint $table) {
            $table->id();
            $table->string('condition_code')->unique();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->string('book_code')->unique(); // Kode unik per eksemplar buku
            $table->enum('condition', [
                'new', 'good', 'fair', 'poor', 'damaged', 
                'lost', 'under_repair', 'withdrawn'
            ])->default('new');
            $table->enum('previous_condition', [
                'new', 'good', 'fair', 'poor', 'damaged', 'lost'
            ])->nullable();
            $table->text('condition_description')->nullable();
            $table->text('damage_description')->nullable(); // Deskripsi kerusakan
            $table->json('damage_details')->nullable(); // Detail kerusakan dalam format JSON
            $table->enum('physical_state', [
                'sangat_baik', 'baik', 'cukup', 'rusak_ringan', 'rusak_berat'
            ])->nullable();
            
            // Tracking peminjaman
            $table->foreignId('current_borrowing_id')->nullable()->constrained('borrowings');
            $table->foreignId('last_borrowing_id')->nullable()->constrained('borrowings');
            $table->integer('times_borrowed')->default(0);
            
            // Pengecekan
            $table->date('last_check_date');
            $table->date('next_check_date')->nullable();
            $table->foreignId('checked_by')->constrained('users');
            $table->text('check_notes')->nullable();
            
            // Perbaikan
            $table->boolean('needs_repair')->default(false);
            $table->date('repair_date')->nullable();
            $table->date('repair_completion_date')->nullable();
            $table->text('repair_notes')->nullable();
            $table->decimal('repair_cost', 15, 2)->nullable();
            
            // Status
            $table->boolean('is_available')->default(true);
            $table->boolean('is_reference_only')->default(false); // Hanya untuk referensi (tidak dipinjam)
            $table->boolean('is_digital')->default(false);
            $table->string('digital_file_path')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('book_id');
            $table->index('condition');
            $table->index('last_check_date');
            $table->index('is_available');
            $table->index('book_code');
            $table->index('condition_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_conditions_logs');
    }
}
