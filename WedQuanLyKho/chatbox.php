<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. CẤU HÌNH KẾT NỐI SERVER AI
// Thay đổi URL này nếu bạn deploy Python lên Cloud (Render, Railway,...)
define('PYTHON_API_URL', 'http://127.0.0.1:10000/api/chat');

// 2. NHẬN DỮ LIỆU TỪ FRONTEND
$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode(['reply' => '❗ Leader chưa nhập nội dung câu hỏi kìa!']);
    exit;
}

try {
    // 3. GỬI YÊU CẦU SANG SERVER PYTHON (FLASK) QUA CURL
    $ch = curl_init(PYTHON_API_URL);
    
    // Chuẩn bị payload theo định dạng mà app.py yêu cầu
    $payload = json_encode(['text' => $message]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);
    
    // Đặt timeout 15 giây để tránh treo web nếu AI xử lý lâu
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // 4. XỬ LÝ KẾT QUẢ TRẢ VỀ
    if ($error) {
        // Trường hợp không kết nối được (Server Python chưa chạy)
        echo json_encode([
            'reply' => '🤖 [Hệ thống]: Server AI đang ngủ gật rồi. Vui lòng kiểm tra lại kết nối (Port 10000)!',
            'status' => 'offline'
        ]);
        exit;
    }

    $result = json_decode($response, true);

    if ($httpCode === 200 && isset($result['response'])) {
        // Trả về câu trả lời từ AI
        echo json_encode([
            'reply' => $result['response'],
            'intent' => $result['intent'] ?? 'unknown',
            'confidence' => $result['confidence'] ?? 0,
            'status' => 'success'
        ]);
    } else {
        // Trường hợp Server Python trả về lỗi (400, 500)
        $msg = $result['message'] ?? 'Lỗi không xác định từ bộ não AI.';
        echo json_encode([
            'reply' => '❌ Lỗi xử lý: ' . $msg,
            'status' => 'error'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'reply' => '⚠️ Hệ thống gặp sự cố kết nối nội bộ.',
        'error' => $e->getMessage()
    ]);
}