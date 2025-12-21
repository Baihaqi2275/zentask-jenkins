<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Avatar/profile photo
            $table->string('avatar')->nullable()->after('email');

            // Subscription plan
            $table->enum('subscription_plan', ['free', 'pro', 'plus'])
                  ->default('free')
                  ->after('avatar');

            // Plan started date (untuk tracking)
            $table->timestamp('plan_started_at')->nullable()->after('subscription_plan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'subscription_plan', 'plan_started_at']);
        });
    }
};
