<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Thêm 2 cột mới cho Ebook
            $table->string('file_size')->nullable()->after('ebook_price'); // Ví dụ: '5.2 MB'
            $table->string('font_family')->nullable()->after('file_size'); // Ví dụ: 'Times New Roman'
            
            // Đổi tên cột từ Năm sang Ngày (Laravel 12 hỗ trợ đổi tên trực tiếp)
            // Lưu ý: Nếu cột cũ của cậu tên là published_date thì dùng dòng này
            $table->renameColumn('published_date', 'published_date');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['file_size', 'font_family']);
            $table->renameColumn('published_date', 'published_date');
        });
    }
};