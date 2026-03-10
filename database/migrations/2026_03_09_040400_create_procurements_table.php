<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->string('procurement_number')->unique();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->foreignId('created_by')->constrained('users'); // Admin yang membuat
            $table->date('procurement_date');
            $table->date('expected_date')->nullable();
            $table->date('received_date')->nullable();
            $table->enum('status', ['pending', 'ordered', 'partial', 'completed', 'cancelled'])
                  ->default('pending');
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('invoice_number')->nullable();
            $table->string('receipt_number')->nullable();
            
            // Indexes
            $table->index('procurement_number');
            $table->index('vendor_id');
            $table->index('status');
            $table->index('procurement_date');
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
        Schema::dropIfExists('procurements');
    }
}
