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
    Schema::create('order_details', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id'); // Thuộc đơn hàng nào
        $table->unsignedBigInteger('book_id');  // Mua sách nào
        $table->integer('quantity');            // Số lượng bao nhiêu
        $table->decimal('price', 10, 2);        // Giá tại thời điểm mua
        $table->timestamps();

        // Khóa ngoại để bảo vệ dữ liệu (Xóa đơn hàng thì xóa luôn chi tiết)
        $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
