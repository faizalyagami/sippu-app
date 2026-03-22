<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBorrowingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrowing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained('borrowings')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books');
            $table->integer('quantity');
            $table->integer('returned_quantity')->default(0);
            $table->integer('damaged_quantity')->default(0);
            $table->integer('lost_quantity')->default(0);
            $table->enum('status', ['pending', 'approved', 'reject', 'completed'])
                ->default('pending');
            $table->text('condition_notes')->nullable();
            $table->date('return_date')->nullable();

            // Indexes
            $table->index('borrowing_id');
            $table->index('book_id');
            $table->index('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('borrowing_items');
    }
}
