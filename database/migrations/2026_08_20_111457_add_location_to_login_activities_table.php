<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('login_activities', function (Blueprint $table) {
            $table->string('location')->nullable()->after('platform');
            $table->boolean('is_suspicious')->default(false)->after('location');
            $table->boolean('is_new_device')->default(false)->after('is_suspicious');
        });
    }

    public function down(): void
    {
        Schema::table('login_activities', function (Blueprint $table) {
            $table->dropColumn(['location', 'is_suspicious', 'is_new_device']);
        });
    }
};
