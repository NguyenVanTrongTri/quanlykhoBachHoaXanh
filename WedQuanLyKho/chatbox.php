<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. Cấu hình URL của Server Python (Thay đổi URL này nếu bạn deploy lên Render/Heroku)
define('PYTHON_API_URL', 'http://127.0.0.1:10000/api/chat');

// Nhận tin nhắn từ Frontend
$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode(['reply' => '❗ Bạn chưa nhập câu hỏi']);
    exit;
}

try {
    // 2. Sử dụng cURL để gửi yêu cầu đến Flask (app.py)
    $ch = curl_init(PYTHON_API_URL);
    
    // Tạo payload để gửi sang Python
    $payload = json_encode(['text' => $message]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);
    
    // Thời gian chờ (timeout) để tránh treo web nếu Python server chậm
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // 3. Xử lý kết quả trả về từ Python
    if ($error) {
        throw new Exception("Không thể kết nối tới Server AI: " . $error);
    }

    if ($httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = $errorData['message'] ?? 'Lỗi không xác định từ Server AI';
        echo json_encode(['reply' => "❌ AI đang bận: $errorMsg"]);
        exit;
    }

    $result = json_decode($response, true);
    
    // Trả về phản hồi từ AI cho Frontend
    // Ở đây $result['response'] chính là câu trả lời từ hàm get_answer() trong Python
    echo json_encode([
        'reply' => $result['response'],
        'intent' => $result['intent'] ?? 'unknown',
        'confidence' => $result['confidence'] ?? 0
    ]);

} catch (Exception $e) {
    echo json_encode([
        'reply' => '⚠️ Hệ thống AI đang bảo trì. Vui lòng thử lại sau!',
        'error_detail' => $e->getMessage()
    ]);
}