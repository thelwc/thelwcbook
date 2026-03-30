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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Tiêu đề lớn
            $table->string('description')->nullable(); // Dòng chữ nhỏ
            $table->string('image'); // Đường dẫn ảnh
            $table->string('link')->default('#'); // Link khi bấm vào nút "Mua ngay"
            $table->string('status')->default('active'); // active hoặc hidden
            $table->integer('order')->default(0); // Thứ tự hiển thị
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
