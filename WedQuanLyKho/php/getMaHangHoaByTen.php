<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../KetNoi/connect.php';

$conn = connectdb();
$conn->exec("SET NAMES utf8mb4");

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['tenhanghoa']) || trim($_GET['tenhanghoa']) === '') {
    echo json_encode(["status" => "error"]);
    exit;
}

$ten = trim($_GET['tenhanghoa']);

$stmt = $conn->prepare("
    SELECT MaHangHoa
    FROM hanghoa
    WHERE TenHangHoa LIKE ?
    LIMIT 1
");

$stmt->execute(['%' . $ten . '%']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo json_encode(["status" => "success", "data" => $row]);
} else {
    echo json_encode(["status" => "not_found"]);
}
exit;
