<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->decimal('tax', 15, 2)->default(0)->after('total_price');
            $table->decimal('discount', 15, 2)->default(0)->after('tax');
            $table->string('category')->nullable()->after('discount');
            $table->integer('confidence_score')->nullable()->after('category');
        });

        Schema::table('receipt_items', function (Blueprint $table) {
            $table->string('category')->nullable()->after('item_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn(['tax', 'discount', 'category', 'confidence_score']);
        });

        Schema::table('receipt_items', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
