<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PublisherController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ReadController; 
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| KHU VỰC LOGIN/LOGOUT (Không được chặn để Admin còn đăng nhập/đăng xuất)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/terms', function () {
    return view('client.pages.terms'); 
})->name('terms');

Route::get('/policy', function () {
    return view('client.pages.policy'); 
})->name('policy');

Auth::routes(['login' => false, 'register' => false, 'logout' => false]);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Login Google
Route::get('auth/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [LoginController::class, 'handleGoogleCallback']);


/*
|--------------------------------------------------------------------------
| 1. KHU VỰC CÔNG KHAI (Đã bọc middleware restrict.staff để chặn Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['restrict.staff'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');
    Route::get('/book/{id}', [HomeController::class, 'detail'])->name('book.detail');
    Route::get('/search', [HomeController::class, 'search'])->name('search');
    Route::get('/danh-muc/{slug}', [HomeController::class, 'categoryBook'])->name('category.show');
    Route::get('/sach/{id}', [HomeController::class, 'Detail'])->name('books.detail');
    Route::get('/ajax-search', [App\Http\Controllers\HomeController::class, 'ajaxSearch'])->name('ajax.search');
    // Tin tức
    Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
    Route::get('/tin-tuc/{id}', [PostController::class, 'show'])->name('posts.show');

    Route::get('/doc-sach/{id}', [ReadController::class, 'read'])->name('book.read');
    Route::get('/book-content/{id}', [ReadController::class, 'getFileContent'])->name('book.content');
    
    // Giỏ hàng & Mua sắm 
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('add.to.cart');
    Route::patch('/update-cart', [CartController::class, 'update'])->name('update.cart');
    Route::delete('/remove-from-cart', [CartController::class, 'remove'])->name('remove.from.cart');

    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::get('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    // Tra cứu đơn hàng (Dành cho khách vãng lai và cả user)
    Route::get('/tra-cuu-don-hang', [OrderController::class, 'trackOrder'])->name('orders.track');
    Route::post('/tra-cuu-don-hang', [OrderController::class, 'trackOrderPost'])->name('orders.track.post');
    Route::post('/place-order', [OrderController::class, 'store'])->name('place.order');
    Route::get('/order/success/{id}', [OrderController::class, 'success'])->name('orders.success');

    Route::get('/shop', [BookController::class, 'shop'])->name('shop');
    Route::get('/deal-soc', [BookController::class, 'flashSale'])->name('flash.sale');
    Route::get('/sach-moi', [BookController::class, 'newArrivals'])->name('new.arrivals');
    Route::get('/sach-ban-chay', [BookController::class, 'bestSellers'])->name('best.sellers');
    Route::get('/ebooks', [App\Http\Controllers\HomeController::class, 'ebooks'])->name('ebooks');
});


/*
|--------------------------------------------------------------------------
| 2.A KHU VỰC CHUNG CHO TÀI KHOẢN ĐĂNG NHẬP (Cả Admin và Khách đều vào được)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Thông báo
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/mark-all-read', function () {
        if(auth()->check()) { auth()->user()->unreadNotifications->markAsRead(); }
        return redirect()->back();
    })->name('notifications.readAll');
    // Route đánh dấu đã đọc 1 thông báo rồi chuyển hướng
    Route::get('/thong-bao/{id}/doc', [App\Http\Controllers\HomeController::class, 'readNotification'])
        ->name('notifications.read')
        ->middleware('auth');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar/delete', [HomeController::class, 'deleteAvatar'])->name('profile.avatar.delete');    
});

/*
|--------------------------------------------------------------------------
| 2.B KHU VỰC CÁ NHÂN CỦA KHÁCH HÀNG (Bị lính gác restrict.staff chặn Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'restrict.staff'])->group(function () {
    Route::get('/tu-sach-cua-toi', [HomeController::class, 'myBooks'])->name('user.my_books');
    Route::get('/minigame', [GameController::class, 'index'])->name('game.index');
    Route::post('/minigame/open', [GameController::class, 'openBox'])->name('game.open');
    Route::get('/wishlist', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorite/{id}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/my-orders', [OrderController::class, 'history'])->name('client.account.history');
    Route::get('/my-orders/{id}', [OrderController::class, 'historyDetail'])->name('client.account.history.detail');
    Route::post('/my-orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Chỗ này vẫn chặn cứng ngắc, Admin không thể vào thanh toán hay xem lịch sử mua
    Route::post('/review/store', [ReviewController::class, 'store'])->name('review.store');
});


/*
|--------------------------------------------------------------------------
| 3. KHU VỰC QUẢN TRỊ (ADMIN PANEL) - DÀNH CHO NHÂN VIÊN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // =================================================================
    // 1. NHÓM "SẾP & QUẢN LÝ" (Dashboard & Tiền bạc & Import)
    // =================================================================
        Route::middleware(['role:admin,director,manager'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        // Export báo cáo (Chỉ xem)
        Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    });
    
    Route::middleware(['role:admin,director,manager,moderator'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        // Quản lý phí ship (Shipping Fee)
        Route::get('/shipping-fee', [App\Http\Controllers\SettingController::class, 'index'])->name('admin.shipping_fee.index');
        Route::post('/shipping-fee', [App\Http\Controllers\SettingController::class, 'update'])->name('admin.shipping_fee.update');
    });

    // 🔥 NHÓM CHỨC NĂNG "NGUY HIỂM" (Chỉ Quản lý được làm)
    Route::middleware(['role:director,manager,moderator'])->group(function () {
        Route::resource('vouchers', VoucherController::class);

        // Import Excel (Rất nguy hiểm, chỉ Quản lý)
        Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
    });

    // =================================================================
    // 2. NHÓM "QUẢN LÝ SẢN PHẨM" (CRUD Sách, Danh mục, NXB)
    // =================================================================
    Route::middleware(['role:manager,staff'])->group(function () {
        Route::resource('publishers', PublisherController::class)->except(['index', 'show']);
        Route::resource('books', BookController::class)->except(['index', 'show', 'import', 'export']);
        Route::resource('categories', CategoryController::class)->except(['index', 'show', 'import', 'export']);
        Route::post('/books/import', [BookController::class, 'import'])->name('books.import');
        Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');
    });

    // =================================================================
    // 3. NHÓM "NỘI DUNG & TRUYỀN THÔNG" (CRUD Banner, Tin tức, Review)
    // =================================================================
    Route::middleware(['role:manager,moderator'])->group(function () {
        // Banner (Kiểm duyệt được sửa xóa banner)
        Route::resource('banners', BannerController::class)->except(['index', 'show']);
        Route::post('banners/{id}/toggle', [BannerController::class, 'toggleStatus'])->name('banners.toggle');

        // Review (Chỉ xóa)
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
        
        // Tin tức (Full quyền)
        Route::get('/posts/create', [PostController::class, 'create'])->name('admin.posts.create');
        Route::post('/posts/store', [PostController::class, 'store'])->name('admin.posts.store');
        Route::get('/posts/{id}/edit', [PostController::class, 'edit'])->name('admin.posts.edit');
        Route::put('/posts/{id}', [PostController::class, 'update'])->name('admin.posts.update');
        Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('admin.posts.destroy'); 
        Route::post('/posts/{id}/toggle', [PostController::class, 'toggleStatus'])->name('admin.posts.toggle');
        // Route để Admin trl, xóa câu trả lời của chính mình
        Route::post('/reviews/{id}/reply', [App\Http\Controllers\ReviewController::class, 'reply'])->name('admin.reviews.reply');
        Route::put('/admin/reviews/{id}/delete-reply', [App\Http\Controllers\ReviewController::class, 'deleteReply'])->name('admin.reviews.delete_reply');
        // Route để Ẩn / Hiện bình luận của khách
        Route::put('/admin/reviews/{id}/toggle-status', [App\Http\Controllers\ReviewController::class, 'toggleStatus'])->name('admin.reviews.toggle_status');

    });

    // =================================================================
    // 4. NHÓM "XEM DỮ LIỆU" (READ-ONLY)
    // =================================================================
    Route::middleware(['role:admin,director,manager,staff,moderator'])->group(function () {
        
        // Xem Đơn hàng
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{id}/print', [OrderController::class, 'printInvoice'])->name('admin.orders.print');
        // Route xem chi tiết sách trong Admin
        Route::get('/admin/books/{id}', [App\Http\Controllers\BookController::class, 'show'])->name('admin.books.show');
        // Xem Sách & Danh mục
        Route::get('/books/export', [BookController::class, 'export'])->name('books.export');
        
        // Danh sách hiển thị (Index & Show)
        Route::resource('publishers', PublisherController::class)->only(['index', 'show']); 
        Route::resource('books', BookController::class)->only(['index', 'show']);
        Route::resource('categories', CategoryController::class)->only(['index', 'show']);
        Route::resource('banners', BannerController::class)->only(['index', 'show']);
        
        // Xem Review & Tin tức
        Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('/posts', [PostController::class, 'adminIndex'])->name('admin.posts.index');

        
    });

    // =================================================================
    // 5. NHÓM "XỬ LÝ ĐƠN HÀNG" (Tác nghiệp)
    // =================================================================
    Route::middleware(['role:manager,staff'])->group(function () {
        Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('/orders/{id}/out-of-stock', [OrderController::class, 'markAsOutOfStock'])->name('orders.outOfStock');
        Route::post('/admin/orders/{id}/bom-hang', [\App\Http\Controllers\OrderController::class, 'markAsBomHang'])->name('admin.orders.bom_hang');
    });

    // =================================================================
    // 6. NHÓM "QUẢN TRỊ USER" (ĐẶC QUYỀN CỦA ADMIN)
    // =================================================================
    Route::middleware(['role:admin,director,manager'])->group(function () {
        Route::resource('users', UserController::class)->except(['import', 'export']);
    });
    Route::middleware(['role:director'])->group(function () {
        Route::get('/admin/revenue/details', [App\Http\Controllers\DashboardController::class, 'revenueDetails'])->name('admin.revenue.details');
        Route::get('/admin/revenue/export', [App\Http\Controllers\DashboardController::class, 'exportExcel'])->name('admin.revenue.export');
        Route::get('/dashboard/urgent-books', [App\Http\Controllers\DashboardController::class, 'urgentBooks'])->name('dashboard.urgent_books');
        Route::get('/dashboard/urgent-books/export', [App\Http\Controllers\DashboardController::class, 'exportUrgentBooks'])->name('dashboard.urgent_books.export');   
    });   
});