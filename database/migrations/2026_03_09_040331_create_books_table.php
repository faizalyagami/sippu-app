<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('isbn')->unique()->nullable();
            $table->string('author');
            $table->string('publisher');
            $table->year('publisher_year');
            $table->foreignId('category_id')->constrained('categories');
            $table->text('description')->nullable();
            $table->string('language')->default('Indonesia');
            $table->integer('pages')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('location_rack')->nullable();
            $table->integer('total_stock')->default(0);
            $table->integer('available_stock')->default(0);
            $table->integer('borrowed_stock')->default(0);
            $table->integer('damaged_stock')->default(0);
            $table->integer('lost_stock')->default(0);
            $table->decimal('price', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('title');
            $table->index('author');
            $table->index('isbn');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
