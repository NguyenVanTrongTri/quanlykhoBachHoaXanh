<?php
session_start();
include __DIR__ . '/../KetNoi/connect.php';
$conn = connectdb();

// Đặt avatar mặc định
$defaultAvatar = 'user.jpg';

// Kiểm tra session
if (isset($_SESSION['MaTK'])) {
    $stmt = $conn->prepare("
        SELECT Avatar
        FROM taikhoan
        WHERE MaTK = ?
    ");
    $stmt->execute([$_SESSION['MaTK']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Lấy avatar nếu có, không thì dùng mặc định
    $avatar = !empty($user['Avatar']) ? $user['Avatar'] : $defaultAvatar;
} else {
    $avatar = $defaultAvatar;
}

// Đường dẫn thực tế tới file avatar
$avatarPath = __DIR__ . "/../uploads/avatar/$avatar";

// Nếu file không tồn tại thì dùng default
if (!file_exists($avatarPath)) {
    $avatarPath = __DIR__ . "/../uploads/avatar/$defaultAvatar";
}

// Lấy định dạng MIME
$info = pathinfo($avatarPath);
$ext = strtolower($info['extension']);
switch($ext) {
    case 'jpg':
    case 'jpeg': $mime = 'image/jpeg'; break;
    case 'png': $mime = 'image/png'; break;
    case 'gif': $mime = 'image/gif'; break;
    default: $mime = 'image/jpeg';
}

// Xuất ảnh ra trình duyệt
header("Content-Type: $mime");
readfile($avatarPath);
exit;
?>
