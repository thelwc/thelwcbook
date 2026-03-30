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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã giảm giá (VD: SALE50)
            $table->enum('type', ['percent', 'fixed']); // Loại: phần trăm hoặc tiền mặt
            $table->decimal('value', 10, 2); // Giá trị giảm (VD: 10% hoặc 50000)
            $table->integer('quantity'); // Số lượng mã
            $table->decimal('min_order_amount', 10, 2)->default(0); // Đơn tối thiểu để dùng mã
            $table->dateTime('start_date')->nullable(); // Ngày bắt đầu
            $table->dateTime('end_date')->nullable(); // Ngày kết thúc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
