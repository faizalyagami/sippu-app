<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Yang mereview (kaprodi/admin)
            $table->foreignId('borrowing_id')->nullable()->constrained('borrowings'); // Jika review setelah pinjam
            $table->tinyInteger('rating')->unsigned()->default(5); // 1-5
            $table->text('review')->nullable();
            $table->text('pros')->nullable(); // Kelebihan buku
            $table->text('cons')->nullable(); // Kekurangan buku
            $table->boolean('is_recommended')->default(true);
            $table->enum('read_status', ['belum_dibaca', 'sedang_dibaca', 'selesai_dibaca'])->nullable();
            $table->json('tags')->nullable(); // Tags untuk review
            $table->integer('helpful_count')->default(0); // Jumlah yang menganggap review membantu
            $table->integer('unhelpful_count')->default(0);
            $table->boolean('is_approved')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('book_id');
            $table->index('user_id');
            $table->index('rating');
            $table->index('created_at');
            
            // Unique constraint untuk mencegah review ganda
            $table->unique(['book_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_reviews');
    }
}
