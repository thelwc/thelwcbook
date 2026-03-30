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
    Schema::table('users', function (Blueprint $table) {
        // Thêm 3 cột mới, cho phép null để không lỗi dữ liệu cũ
        $table->string('phone')->nullable()->after('email');
        $table->string('address')->nullable()->after('phone');
        $table->string('avatar')->nullable()->after('address');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
