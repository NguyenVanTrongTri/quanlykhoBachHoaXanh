<?php
session_start();
include '../KetNoi/connect.php';
$conn = connectdb();

header('Content-Type: application/json; charset=utf-8');

// Kiểm tra tham số
if (!isset($_GET['maloaikho']) || trim($_GET['maloaikho']) === '') {
    echo json_encode(["status" => "error", "message" => "Thiếu mã loại kho"]);
    exit;
}

$ma = trim($_GET['maloaikho']);

// Truy vấn thông tin loại kho
$stmt = $conn->prepare("
    SELECT MaLoaiKho, TenLoaiKho, NhietDo, TongSucChua, DaChua, ConTrong, MoTa, TrangThai, GhiChu
    FROM loaikho
    WHERE MaLoaiKho = ?
    LIMIT 1
");
$stmt->execute([$ma]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Trả về kết quả
if ($row) {
    echo json_encode(["status" => "success", "data" => $row]);
} else {
    echo json_encode(["status" => "not_found", "message" => "Không tìm thấy mã loại kho"]);
}
exit;
?>
