<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // Giữ lại cái cũ phòng khi cần dùng
            'admin.auth' => \App\Http\Middleware\CheckAdmin::class,
            
            // === THÊM CÁI MỚI VÀO ĐÂY ===
            'role' => \App\Http\Middleware\CheckRole::class,
            'force.admin' => \App\Http\Middleware\ForceAdmin::class,
            'restrict.staff' => \App\Http\Middleware\RestrictStaffFromFrontend::class,
        
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();