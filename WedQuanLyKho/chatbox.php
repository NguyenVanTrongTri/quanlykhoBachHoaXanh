<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. Cấu hình địa chỉ Server Python (app.py)
// Thay địa chỉ này bằng link Render hoặc Local của bạn
$PYTHON_API_URL = "https://lora-ai-9ti1.onrender.com/api/chat";

$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode(['reply' => '❗ Bạn chưa nhập câu hỏi']);
    exit;
}

try {
    // 2. Sử dụng CURL để gửi yêu cầu sang app.py
    $ch = curl_init($PYTHON_API_URL);
    
    // Cấu hình dữ liệu gửi đi dạng JSON
    $payload = json_encode(array("text" => $message));
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Chờ tối đa 10s

    // 3. Thực thi và nhận phản hồi từ Python
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if(curl_errno($ch)){
        throw new Exception(curl_error($ch));
    }
    curl_close($ch);

    // 4. Xử lý kết quả trả về
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        
        // Trả về nội dung mà AI đã xử lý (đã lấy từ field 'response' trong app.py)
        echo json_encode([
            'reply' => $result['response'] ?? '🤖 AI không phản hồi nội dung.',
            'intent' => $result['intent'] ?? 'unknown',
            'confidence' => $result['confidence'] ?? 0
        ]);
    } else {
        echo json_encode([
            'reply' => '❌ Server AI đang bận (Mã lỗi: ' . $httpCode . ')'
        ]);
    }

} catch (Exception $e) {
    // Trường hợp app.py chưa bật hoặc lỗi kết nối
    echo json_encode([
        'reply' => '🔌 Không thể kết nối với bộ não AI. Vui lòng kiểm tra lại Server Python!'
    ]);
}