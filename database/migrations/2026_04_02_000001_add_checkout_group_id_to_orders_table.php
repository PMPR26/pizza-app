<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'checkout_group_id')) {
                $table->uuid('checkout_group_id')->nullable()->after('id');
                $table->index('checkout_group_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'checkout_group_id')) {
                $table->dropIndex(['checkout_group_id']);
                $table->dropColumn('checkout_group_id');
            }
        });
    }
};
