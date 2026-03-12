<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementItemsTable extends Migration
{
    public function up()
    {
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_id')->constrained('procurements')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books');
            $table->integer('quantity');
            $table->integer('received_quantity')->default(0);
            $table->integer('damaged_quantity')->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'ordered', 'partial', 'completed', 'cancelled'])
                  ->default('pending');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('procurement_id');
            $table->index('book_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('procurement_items');
    }
}