<?php
session_start();
include_once 'KetNoi/connect.php';
$conn = connectdb();

if (!isset($_SESSION['MaTK'])) {
    echo json_encode(null);
    exit;
}

$stmt = $conn->prepare("
    SELECT tk.TenDangNhap, tk.Email, nv.HoTen, nv.ChucVu, nv.NgaySinh, nv.GioiTinh, nv.TrangThai,
           nv.DiaChi, nv.GhiChu, nv.SDT
    FROM taikhoan tk
    INNER JOIN nhanvien nv ON tk.MaNV = nv.MaNV
    WHERE tk.MaTK = ?
");
$stmt->execute([$_SESSION['MaTK']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($user);
