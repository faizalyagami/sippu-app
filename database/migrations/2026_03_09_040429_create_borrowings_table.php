<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBorrowingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->string('borrowing_number')->unique();
            $table->foreignId('user_id')->constrained('users'); // Kaprodi yang meminjam
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Admin yang menyetujui
            $table->date('borrowing_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'borrowed', 'returned', 'overdue', 'cancelled'])
                ->default('pending');
            $table->text('purpose')->nullable(); // Tujuan peminjaman
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->integer('total_items')->default(0);
            $table->timestamps();
            // Indexes
            $table->index('borrowing_number');
            $table->index('user_id');
            $table->index('status');
            $table->index('borrowing_date');
            $table->index('expected_return_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('borrowings');
    }
}
