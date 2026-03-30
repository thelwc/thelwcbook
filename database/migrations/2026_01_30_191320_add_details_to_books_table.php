<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            // 1. Thông tin chi tiết sách
            $table->integer('published_date')->nullable()->after('publisher_id'); // Năm xuất bản
            $table->string('cover_type')->default('Bìa Mềm')->after('published_date'); // Bìa cứng/mềm
            $table->string('dimensions')->nullable()->after('cover_type'); // Khổ giấy (VD: 13x19 cm)
            $table->integer('page_count')->nullable()->after('dimensions'); // Số trang

            // 2. Sách nước ngoài & Dịch giả
            $table->boolean('is_foreign')->default(false)->after('page_count'); // 0: Trong nước, 1: Nước ngoài
            $table->string('translator')->nullable()->after('is_foreign'); // Tên dịch giả

            // 3. Ebook & Đọc thử
            $table->string('file_preview')->nullable()->after('image'); // File PDF đọc thử (vài trang)
            $table->string('file_ebook')->nullable()->after('file_preview');   // File Ebook full (để bán)
            $table->decimal('ebook_price', 10, 0)->nullable()->after('price'); // Giá bán Ebook riêng
        });
    }

    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'published_date', 'cover_type', 'dimensions', 'page_count',
                'is_foreign', 'translator',
                'file_preview', 'file_ebook', 'ebook_price'
            ]);
        });
    }
};