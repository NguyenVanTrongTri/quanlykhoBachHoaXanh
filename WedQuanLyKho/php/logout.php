<?php
session_start();
include '../KetNoi/connect.php';
$conn = connectdb();

if (isset($_SESSION['MaTK'])) {
    $update = $conn->prepare("
        UPDATE taikhoan 
        SET isOnline = 0,
            trangthaihoatdong = NOW()
        WHERE MaTK = ?
    ");
    $update->execute([$_SESSION['MaTK']]);
}

session_unset();
session_destroy();

header("Location: ../Login.php");
exit();
?>