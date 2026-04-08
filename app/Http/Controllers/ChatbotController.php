<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
// Gọi Model Book (Sách) thay vì Product
use App\Models\Book;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            $userMessage = $request->input('message');
            $apiKey = env('GEMINI_API_KEY');

            // ==========================================
            // 1. CHUI VÀO KHO SÁCH (DATABASE) THELWC BOOKS
            // ==========================================
            $booksData = Book::with('category')->orderBy('id', 'desc')->take(50)->get();

            $aiBooks = $booksData->map(function($book) {
                $price = (float)($book->price ?? 0);
                $salePrice = (float)($book->sale_price ?? 0);
                $ebookPrice = (float)($book->ebook_price ?? 0);
                
                $isSale = ($salePrice > 0 && $salePrice < $price);
                $currentPrice = $isSale ? $salePrice : $price;

                // Tính tổng lượt bán = Sách giấy + Ebook
                $totalSold = ($book->total_sold ?? 0) + ($book->ebook_sold ?? 0);
                
                return [
                    'tên_sách'     => $book->title,
                    'tác_giả'      => $book->author,
                    'thể_loại'     => $book->category ? $book->category->name : 'Tổng hợp',
                    'giá_sách_giấy'=> number_format($currentPrice) . 'đ',
                    'giá_số_học'   => $currentPrice, 
                    'có_bản_ebook' => ($ebookPrice > 0 || !empty($book->file_ebook)) ? 'Có' : 'Không',
                    'giá_ebook_hiển_thị' => ($ebookPrice > 0) ? number_format($ebookPrice) . 'đ' : '0đ',
                    'giá_ebook_số_học'   => $ebookPrice, 
                    'đang_khuyến_mãi' => $isSale ? 'Có' : 'Không',
                    'tổng_lượt_bán'=> $totalSold, // Bơm thêm lượt bán cho AI
                    'trạng_thái'   => ($book->quantity > 0) ? "Còn hàng" : "Hết hàng",
                ];
            });

            // ==========================================
            // 🔥 LARAVEL TỰ TÍNH TOÁN SẴN (THÊM SÁCH MỚI) 🔥
            // ==========================================
            // Vì danh sách đã orderBy('id', 'desc') nên cuốn đầu tiên chính là cuốn mới nhất
            $newestBook = $aiBooks->first(); 
            
            $cheapestEbook = $aiBooks->filter(function($b) { return $b['có_bản_ebook'] == 'Có' && $b['giá_ebook_số_học'] > 0; })->sortBy('giá_ebook_số_học')->first();
            $mostExpensiveBook = $aiBooks->sortByDesc('giá_số_học')->first();
            $cheapestBook = $aiBooks->filter(function($b) { return $b['giá_số_học'] > 0; })->sortBy('giá_số_học')->first();
            $bestSellingBook = $aiBooks->sortByDesc('tổng_lượt_bán')->first();

            $thongKeNhanh = "THỐNG KÊ NHANH (Dùng thông tin này để trả lời NGAY LẬP TỨC): "
                          . "- Sách MỚI NHẤT vừa lên kệ: " . ($newestBook ? $newestBook['tên_sách'] . " (Giá: " . $newestBook['giá_sách_giấy'] . ")" : "Đang cập nhật") . ". "
                          . "- Ebook rẻ nhất: " . ($cheapestEbook ? $cheapestEbook['tên_sách'] . " (Giá: " . $cheapestEbook['giá_ebook_hiển_thị'] . ")" : "Đang cập nhật") . ". "
                          . "- Sách in đắt nhất: " . ($mostExpensiveBook ? $mostExpensiveBook['tên_sách'] . " (Giá: " . $mostExpensiveBook['giá_sách_giấy'] . ")" : "") . ". "
                          . "- Sách in rẻ nhất: " . ($cheapestBook ? $cheapestBook['tên_sách'] . " (Giá: " . $cheapestBook['giá_sách_giấy'] . ")" : "") . ". "
                          . "- Sách bán chạy nhất: " . ($bestSellingBook ? $bestSellingBook['tên_sách'] . " (Đã bán: " . $bestSellingBook['tổng_lượt_bán'] . " cuốn)" : "Đang cập nhật") . ". ";

            // ==========================================
            // 2. NHÉT KIẾN THỨC VÀO NÃO CON AI (PROMPT)
            // ==========================================
            $prompt = "Bạn là trợ lý ảo AI thông minh của nhà sách Thelwc Books. "
                    . "THÔNG TIN LIÊN HỆ: SĐT 0964617664, ĐC: Long Xuyên, An Giang. "
                    . $thongKeNhanh
                    . "KHO SÁCH CHI TIẾT: " 
                    . $aiBooks->toJson(JSON_UNESCAPED_UNICODE) 
                    . ". QUY TẮC TUYỆT ĐỐI: "
                    . "1. Khi khách hỏi sách nào MỚI NHẤT, BÁN CHẠY NHẤT, ĐẮT NHẤT hoặc RẺ NHẤT, BẮT BUỘC phải đọc trong phần 'THỐNG KÊ NHANH' để trả lời khách một cách tự hào. "
                    . "2. Tư vấn sách: Khách muốn mua thì nhắc đăng nhập tài khoản. "
                    . "3. Giao tiếp: Tự nhiên, thân thiện, xưng 'tớ' và 'cậu', dùng emoji. "
                    . "Khách nhắn: " . $userMessage;
            // ==========================================
            // 3. GỌI BẢN GEMINI 2.5 FLASH "BẤT TỬ"
            // ==========================================
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->post("https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . $apiKey, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ]
                ]);

            $data = $response->json();

            // ==========================================
            // 4. TRẢ LỜI KHÁCH HÀNG
            // ==========================================
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $botReply = $data['candidates'][0]['content']['parts'][0]['text'];
            } elseif (isset($data['error'])) {
                $botReply = "Hệ thống đang lỗi xíu sếp ơi: " . $data['error']['message'];
            } else {
                $botReply = "Thelwc AI đang bận xếp lại kệ sách, cậu đợi tớ 1 phút nhé! 📚";
            }

            return response()->json(['reply' => $botReply]);
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Lỗi đứt cáp: ' . $e->getMessage()], 500);
        }
    }
}
