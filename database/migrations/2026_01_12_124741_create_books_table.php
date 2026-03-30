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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            
            // 1. Thông tin cơ bản (Giống quần áo)
            $table->string('title');            // Tên sách
            $table->decimal('price', 10, 0);    // Giá bán
            $table->integer('quantity');        // Số lượng trong kho
            $table->string('image')->nullable(); // Đường dẫn ảnh bìa
            $table->text('description')->nullable(); // Mô tả nội dung
            
            // 2. Thông tin riêng của Sách (Thay cho Size/Màu)
            $table->string('author');           // Tác giả (Bắt buộc phải có)
            $table->string('publisher')->nullable(); // Nhà xuất bản
            
            // 3. Liên kết danh mục (Nếu m chưa tạo bảng categories thì tạm thời comment dòng dưới lại)
            // $table->unsignedBigInteger('category_id')->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
