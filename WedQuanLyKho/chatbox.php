<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'KetNoi/connect.php';

// 1. Kết nối Database (Giữ nguyên để check quyền hoặc log tin nhắn nếu cần)
try {
    $conn = connectdb();
} catch (Throwable $e) {
    echo json_encode(['reply' => '❌ Lỗi kết nối hệ thống dữ liệu']);
    exit;
}

// 2. Lấy dữ liệu tin nhắn từ Frontend
$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode(['reply' => '❗ Trọng ơi, bạn chưa nhập nội dung chat kìa!']);
    exit;
}

// 3. Cấu hình gọi API Lora AI trên Render
$lora_api_url = "https://lora-ai-9ti1.onrender.com/api/chat";

// Chuẩn bị dữ liệu gửi đi (JSON)
$post_data = json_encode(['text' => $message]);

// 4. Thực hiện gọi API bằng CURL
$ch = curl_init($lora_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 20); // Đợi tối đa 20 giây (phòng trường hợp Render đang dậy)

$response = curl_exec($ch);
$error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 5. Xử lý kết quả trả về
if ($error) {
    // Nếu lỗi kết nối (Render đang ngủ hoặc chết link)
    $reply = "🤖 Lora AI đang khởi động (Wake up)... Bạn vui lòng đợi 15s rồi hỏi lại nhé!";
} else {
    $resData = json_decode($response, true);
    
    if ($http_code === 200 && isset($resData['response'])) {
        // Lấy câu trả lời từ AI Python
        $reply = $resData['response'];
    } else {
        // Lỗi logic từ phía Server Python
        $reply = "🤖 Lora đang gặp chút vấn đề về bộ não, Trọng thử lại sau nhé!";
    }
}

// 6. Trả kết quả về cho Giao diện Web (Javascript)
echo json_encode(['reply' => $reply]);