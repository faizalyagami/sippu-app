<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('user_id')->constrained('users'); // Kaprodi/User yang request
            $table->string('book_title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('edition')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->integer('quantity_requested')->default(1);
            $table->text('reason')->nullable();
            $table->text('specifications')->nullable(); // Spesifikasi khusus
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', [
                'pending', 
                'under_review', 
                'approved', 
                'procured', 
                'rejected', 
                'cancelled',
                'completed'
            ])->default('pending');
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_date')->nullable();
            $table->foreignId('procurement_id')->nullable()->constrained('procurements');
            $table->timestamps();
            $table->softDeletes(); // Untuk arsip
            
            // Indexes
            $table->index('request_number');
            $table->index('user_id');
            $table->index('status');
            $table->index('priority');
            $table->index('created_at');
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
        Schema::dropIfExists('book_requests');
    }
}
