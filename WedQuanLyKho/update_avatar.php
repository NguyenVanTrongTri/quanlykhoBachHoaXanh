<?php
session_start();
include_once 'KetNoi/connect.php';
$conn = connectdb();

if(isset($_FILES['avatar']) && isset($_SESSION['MaTK'])) {
    $file = $_FILES['avatar'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = 'avatar_' . $_SESSION['MaTK'] . '.' . $ext;

    // Thư mục upload, sử dụng đường dẫn tuyệt đối
    $uploadDir = __DIR__ . '/images/';
    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // tạo thư mục nếu chưa tồn tại
    }
    $uploadPath = $uploadDir . $newName;

    if(move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Cập nhật vào bảng NhanVien
        $stmt = $conn->prepare("UPDATE nhanvien nv
                                INNER JOIN taikhoan tk ON tk.MaNV = nv.MaNV
                                SET nv.Avatar = ?
                                WHERE tk.MaTK = ?");
        $stmt->execute(['images/' . $newName, $_SESSION['MaTK']]);

        echo json_encode(['success' => true, 'url' => '/DoAnThucTapNghiep/images/' . $newName]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể upload file.']);
    }
}
?>
