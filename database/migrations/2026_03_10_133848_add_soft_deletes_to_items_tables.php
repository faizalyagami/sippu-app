<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToItemsTables extends Migration
{
    private $tables = [
        'borrowing_items',
        'procurement_items'
    ];

    public function up()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->softDeletes();
                });
                
                echo "Added soft deletes to table: {$tableName}\n";
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
                
                echo "Removed soft deletes from table: {$tableName}\n";
            }
        }
    }
}