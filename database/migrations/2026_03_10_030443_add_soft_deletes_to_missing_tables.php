<?php
// database/migrations/2026_03_10_024221_add_soft_deletes_to_missing_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToMissingTables extends Migration
{
    private $tables = [
        'notifications',
        'activity_logs',
        'book_conditions',
        'book_condition_histories',
        'book_review_comments',
        'review_helpful_votes',
        'reservation_queues'
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