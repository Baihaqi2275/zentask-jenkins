<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'completed') DEFAULT 'pending'");
    }
};
