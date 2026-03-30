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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('book_id');
            
            $table->integer('rating'); // Số sao (1 đến 5)
            $table->text('comment')->nullable(); // Nội dung bình luận
            
            // Trạng thái (để Admin duyệt nếu cần, mặc định là hiện luôn)
            $table->enum('status', ['active', 'hidden'])->default('active'); 
            
            $table->timestamps();

            // 🔥 RÀNG BUỘC QUAN TRỌNG:
            // Cặp (user_id, book_id) phải là duy nhất. 
            // Tức là 1 user chỉ được tạo 1 dòng dữ liệu cho 1 cuốn sách.
            $table->unique(['user_id', 'book_id']);

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
