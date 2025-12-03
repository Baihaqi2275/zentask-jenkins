<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){
        Schema::table('events', function(Blueprint $table){
            $table->unsignedBigInteger('category_id')->nullable()->after('color');
            $table->string('rrule')->nullable()->after('all_day'); // e.g. "FREQ=WEEKLY;INTERVAL=1"
            $table->boolean('is_recurring')->default(false)->after('rrule');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }
    public function down(){
        Schema::table('events', function(Blueprint $table){
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id','rrule','is_recurring']);
        });
    }
};
