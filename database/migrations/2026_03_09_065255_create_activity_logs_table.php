<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_number')->unique()->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable(); // Backup nama user jika user dihapus
            $table->string('user_email')->nullable(); // Backup email user
            $table->string('user_role')->nullable(); // Role user saat itu
            $table->string('action'); // create, read, update, delete, login, logout, etc
            $table->string('module'); // books, procurements, borrowings, users, categories, etc
            $table->string('sub_module')->nullable(); // Lebih spesifik
            $table->string('event'); // Deskripsi singkat event
            $table->text('description'); // Deskripsi detail
            $table->json('old_data')->nullable(); // Data sebelum perubahan
            $table->json('new_data')->nullable(); // Data setelah perubahan
            $table->json('changes')->nullable(); // Hanya field yang berubah
            $table->string('model')->nullable(); // Model yang terlibat (App\Models\Book)
            $table->unsignedBigInteger('model_id')->nullable(); // ID dari model
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('device')->nullable();
            $table->string('url')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE
            $table->text('request_data')->nullable(); // Data request
            $table->integer('response_status')->nullable(); // HTTP status code
            $table->string('duration')->nullable(); // Waktu eksekusi
            $table->timestamps();
            
            // Indexes untuk pencarian
            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index('model');
            $table->index('model_id');
            $table->index('created_at');
            $table->index('ip_address');
            
            // Fulltext index untuk pencarian teks
            $table->fullText(['description', 'event']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
