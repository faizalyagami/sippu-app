<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookConditionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_condition_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books');
            $table->enum('condition', ['new', 'good', 'fair', 'poor', 'damaged', 'lost']);
            $table->integer('quantity')->default(0);
            $table->text('description')->nullable();
            $table->date('last_check_date');
            $table->foreignId('checked_by')->constrained('users');
            $table->timestamps();
            
            $table->index('book_id');
            $table->index('condition');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_condition_histories');
    }
}
