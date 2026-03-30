<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View; // <--- 1. BẮT BUỘC PHẢI CÓ DÒNG NÀY
use App\Models\Category;         
use Illuminate\Pagination\Paginator; // 🔥 Thêm dòng này    // <--- 2. Gọi Model Category

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Paginator::defaultView('pagination.custom'); // 🔥 Cài đặt view phân trang mặc định cho toàn bộ ứng dụng
        // Fix lỗi độ dài chuỗi cho MySQL cũ
        Schema::defaultStringLength(191);

        // Chia sẻ biến $categories_menu cho toàn bộ View
        View::composer('*', function ($view) {
            // Kiểm tra xem bảng categories đã có chưa (để tránh lỗi khi chạy lệnh migrate)
            if (Schema::hasTable('categories')) {
                $view->with('categories_menu', Category::all());
            }
        });
    }
}