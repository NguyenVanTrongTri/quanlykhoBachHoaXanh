<?php
session_start();
include '../KetNoi/connect.php';
$conn = connectdb();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['mahanghoa']) || trim($_GET['mahanghoa']) === '') {
    echo json_encode(["status" => "error"]);
    exit;
}

$ma = trim($_GET['mahanghoa']);

$stmt = $conn->prepare("
    SELECT TenHangHoa
    FROM hanghoa
    WHERE MaHangHoa LIKE ?
    LIMIT 1
");
$stmt->execute([$ma]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo json_encode(["status" => "success", "data" => $row]);
} else {
    echo json_encode(["status" => "not_found"]);
}
exit;
