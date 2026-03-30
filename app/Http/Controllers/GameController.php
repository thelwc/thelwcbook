<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Voucher;
use Illuminate\Support\Facades\Cache; 
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    // 1. Giao diện
    public function index()
    {
        $userId = Auth::id();
        $cacheKey = 'game_cooldown_' . $userId; 

        $canPlay = true; // Mặc định luôn cho chơi
        $timeLeft = '';

        // --- 🔴 ĐÃ TẮT: KIỂM TRA GIỜ ---
         if (Cache::has($cacheKey)) {
             $nextPlayTime = Cache::get($cacheKey);
             $now = Carbon::now();

             if ($now->lt($nextPlayTime)) {
                 $canPlay = false;
                 $diff = $now->diff($nextPlayTime);
                 $timeLeft = $diff->h . ' giờ ' . $diff->i . ' phút';
             }
         }

        return view('client.game.box', compact('canPlay', 'timeLeft'));
    }

    // 2. Xử lý mở hộp
    public function openBox()
    {
        $userId = Auth::id();
        $cacheKey = 'game_cooldown_' . $userId;

        // --- 🔴 ĐÃ TẮT: CHẶN SERVER-SIDE ---
         if (Cache::has($cacheKey)) {
             $nextPlayTime = Cache::get($cacheKey);
             if (Carbon::now()->lt($nextPlayTime)) {
                 return response()->json([
                     'type' => 'error', 
                     'name' => 'Gian lận à? Chưa đến giờ quay lại đâu! 😡'
                 ]);
        }
        }

        // ... (Logic random giải thưởng GIỮ NGUYÊN) ...
        $prizes = [
            ['name' => 'Chúc may mắn lần sau 😅', 'type' => 'lose', 'percent' => 60],
            ['name' => 'Voucher giảm 10k',        'type' => 'win',  'percent' => 30, 'prefix' => 'GAME10', 'val' => 10000],
            ['name' => 'Voucher giảm 20k',        'type' => 'win',  'percent' => 9,  'prefix' => 'GAME20', 'val' => 20000],
            ['name' => 'Voucher giảm 50k (VIP)',  'type' => 'win',  'percent' => 1,  'prefix' => 'VIP50',  'val' => 50000],
        ];

        $rand = rand(1, 100);
        $current = 0;
        $result = null;

        foreach ($prizes as $prize) {
            $current += $prize['percent'];
            if ($rand <= $current) {
                $result = $prize;
                break;
            }
        }

        if ($result['type'] == 'win') {
            $uniqueCode = $result['prefix'] . '-' . strtoupper(Str::random(5));
            $result['code'] = $uniqueCode;
            
            Voucher::create([
                'code' => $uniqueCode,
                'type' => 'fixed',
                'value' => $result['val'],
                'quantity' => 1,
                'min_order_amount' => 0,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays(3),
                'user_id' => $userId, // Đã có fix user_id
            ]);
        }

        // --- 🔴 ĐÃ TẮT: LƯU THỜI GIAN CHỜ ---
        Cache::put($cacheKey, Carbon::now()->addHours(24), Carbon::now()->addHours(24));

        return response()->json($result);
    }
}