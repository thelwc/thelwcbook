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
        // Giữ dòng này để tạo google_id
        if (!Schema::hasColumn('users', 'google_id')) {
            $table->string('google_id')->nullable()->after('email');
        }

        // Giữ dòng này để cho phép password rỗng
        $table->string('password')->nullable()->change();

        // --- XÓA HOẶC ẨN DÒNG DƯỚI NÀY ĐI ---
        // $table->string('avatar')->nullable(); 
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
