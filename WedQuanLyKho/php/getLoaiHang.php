<?php
session_start();
include '../KetNoi/connect.php';
$conn = connectdb();

header('Content-Type: application/json; charset=utf-8');

if (empty($_GET['maloaihang'])) {
    echo json_encode(["status" => "error"]);
    exit;
}

$ma = trim($_GET['maloaihang']);

$stmt = $conn->prepare("
    SELECT TenLoaiHang
    FROM loaihanghoa
    WHERE MaLoaiHang = ?
    LIMIT 1
");
$stmt->execute([$ma]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo json_encode([
        "status" => "success",
        "data" => $row
    ]);
} else {
    echo json_encode([
        "status" => "not_found"
    ]);
}
exit;
