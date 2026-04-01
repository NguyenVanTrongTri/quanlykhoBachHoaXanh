<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. Chỉ kết nối DB nếu thực sự cần lưu log (giảm thời gian load)
require_once 'KetNoi/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode(['reply' => '❗ Bạn chưa nhập nội dung chat kìa!']);
    exit;
}

$lora_api_url = "https://lora-ai-9ti1.onrender.com/api/chat";
$post_data = json_encode(['text' => $message]);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $lora_api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $post_data,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Connection: Keep-Alive'], // Giữ kết nối
    CURLOPT_TIMEOUT => 30, // Tăng timeout lên 30s để đợi Render nạp Model nặng
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, // Ép dùng HTTP 1.1 để giữ Keep-Alive tốt hơn
    CURLOPT_SSL_VERIFYPEER => false // Bỏ qua check SSL nếu server nội bộ chậm
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response) {
    $resData = json_decode($response, true);
    $reply = ($http_code === 200 && isset($resData['response'])) 
             ? $resData['response'] 
             : "🤖 Lora đang xử lý dữ liệu, Trọng đợi xíu nhé!";
} else {
    $reply = "🤖 Lora AI đang khởi động... Vui lòng thử lại sau 15 giây!";
}

echo json_encode(['reply' => $reply]);