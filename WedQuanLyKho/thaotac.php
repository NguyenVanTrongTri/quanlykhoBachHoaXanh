<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy tên file hiện tại đang chạy
$current_page = basename($_SERVER['PHP_SELF']);

// Danh sách các trang KHÔNG cần kiểm tra đăng nhập
$public_pages = ['Login.php', 'TaoTaiKhoan.php'];

if (!in_array($current_page, $public_pages)) {
    // Chỉ kiểm tra session nếu KHÔNG nằm trong danh sách trang công khai
    if (!isset($_SESSION['MaTK'])) {
        header("Location: Login.php");
        exit();
    }
    
    // Kiểm tra timeout (giữ nguyên logic cũ của Trọng)
    $timeout = 900; // 15 phút
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
        // ... code update database và destroy session ...
        header("Location: Login.php");
        exit();
    }
}
$_SESSION['LAST_ACTIVITY'] = time();
?>