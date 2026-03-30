<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Sửa thành tinyInteger để lưu số cho nhẹ
            // Quy định: 0 = Admin, 1 = Staff, 2 = Customer
            $table->tinyInteger('role')->default(2)->after('password')->comment('0: Admin, 1: Staff, 2: Customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa cột role nếu chạy rollback
            $table->dropColumn('role');
        });
    }
};