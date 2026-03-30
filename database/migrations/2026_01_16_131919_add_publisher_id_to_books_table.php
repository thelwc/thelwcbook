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
        Schema::table('books', function (Blueprint $table) {
            // Thêm khóa ngoại, cho phép null (để không lỗi dữ liệu cũ)
            $table->foreignId('publisher_id')->nullable()->constrained('publishers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['publisher_id']);
            $table->dropColumn('publisher_id');
        });
    }
};
