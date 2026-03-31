<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../tcpdf/tcpdf.php');
require_once('../KetNoi/connect.php');

$conn = connectdb();
$maPhieuNhap = $_GET['maphieunhap'] ?? '';

if (empty($maPhieuNhap)) {
    die("Không có mã phiếu nhập");
}

$sql = "SELECT * FROM phieunhap WHERE MaPhieuNhap = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$maPhieuNhap]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Không tìm thấy phiếu nhập");
}

// Tạo PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 12);

// Tiêu đề
$pdf->Cell(0, 10, 'HÓA ĐƠN NHẬP KHO', 0, 1, 'C');
$pdf->Ln(5);

// Nội dung
$pdf->Cell(50, 10, 'Mã phiếu nhập:');
$pdf->Cell(0, 10, $data['MaPhieuNhap'], 0, 1);

$pdf->Cell(50, 10, 'Đơn vị:');
$pdf->Cell(0, 10, $data['DonVi'], 0, 1);

$pdf->Cell(50, 10, 'Bộ phận:');
$pdf->Cell(0, 10, $data['BoPhan'], 0, 1);

$pdf->Cell(50, 10, 'Người giao hàng:');
$pdf->Cell(0, 10, $data['NguoiGiaoHang'], 0, 1);

$pdf->Cell(50, 10, 'Địa chỉ:');
$pdf->Cell(0, 10, $data['DiaChi'], 0, 1);

$pdf->Cell(50, 10, 'Thời gian:');
$pdf->Cell(0, 10, $data['ThoiGian'], 0, 1);

$pdf->Cell(50, 10, 'Địa điểm:');
$pdf->Cell(0, 10, $data['DiaDiem'], 0, 1);

$pdf->Cell(50, 10, 'Nhập tại kho:');
$pdf->Cell(0, 10, $data['NhapTaiKho'], 0, 1);

// Xuất file
$pdf->Output("HoaDon_$maPhieuNhap.pdf", "D");
exit;