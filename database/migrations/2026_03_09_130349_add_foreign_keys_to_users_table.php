<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah role_id jika belum ada
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->constrained('roles');
            }

            // Tambah supplier_id jika belum ada
            if (!Schema::hasColumn('users', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('vendors');
            }

            // Tambah photo jika belum ada
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable();
            }

            // Tambah soft deletes jika belum ada
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }

            // Tambah created_at & updated_at jika belum ada
            if (!Schema::hasColumn('users', 'created_at')) {
                $table->timestamps();
            }

            // Indexes
            $table->index('role_id');
            $table->index('supplier_id');
            $table->index('department');
            $table->index('faculty');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['supplier_id']);
            
            $table->dropColumn([
                'role_id',
                'supplier_id',
                'photo',
                'deleted_at'
            ]);
        });
    }
}