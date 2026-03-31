<?php
// Cho phép script chạy mãi mãi không bị timeout
set_time_limit(0); 
// Vẫn tiếp tục chạy ngay cả khi trình duyệt đã đóng (hoặc mất kết nối)
ignore_user_abort(true); 

$url = "https://quanlykhobachhoaxanh.onrender.com"; 

function pingRender($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (WakeUpBot-TrongTri)");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode;
}

// Vòng lặp vô hạn giống như @Scheduled trong Java
while (true) {
    $status = pingRender($url);
    
    // In ra log (nếu chạy qua Terminal/Command Line)
    echo "[" . date("Y-m-d H:i:s") . "] Dang danh thuc Render... Status: " . $status . PHP_EOL;

    // Nghỉ 600 giây (tương đương 10 phút) rồi mới lặp lại
    // Giống hệt fixedRate = 600000 trong Java của Trọng
    sleep(600); 
}
?>