<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once 'KetNoi/connect.php';

try {
    $conn = connectdb();
} catch (Throwable $e) {
    echo json_encode([
        'reply' => '❌ Lỗi kết nối CSDL'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data['message'] ?? '');

if ($message === '') {
    echo json_encode(['reply' => '❗ Bạn chưa nhập câu hỏi']);
    exit;
}

function detectIntent($text){
    $text = mb_strtolower($text, 'UTF-8');
    if (mb_strpos($text, 'xin chào')!== false || mb_strpos($text, 'hello')!== false|| mb_strpos($text, 'hi')!== false)
        return 'XIN_CHAO';
    if (mb_strpos($text, 'mệt')!== false)
        return 'MET_QUA';
    
    if (mb_strpos($text, 'ngày hôm nay')!== false || mb_strpos($text, 'hôm nay')!== false)
        return 'NGAY_HOM_NAY';
    if (
        mb_strpos($text, 'mấy giờ') !== false || 
        mb_strpos($text, 'giờ hiện tại') !== false ||
        mb_strpos($text, 'bây giờ') !== false ||
        mb_strpos($text, 'giờ') !== false
    ) {
        return 'GIO_HIEN_TAI';
    }

    if (mb_strpos($text, 'tồn') !== false) return 'TON_KHO';
    if (mb_strpos($text, 'phiếu nhập') !== false) return 'PHIEU_NHAP';
    if (mb_strpos($text, 'phiếu xuất') !== false) return 'PHIEU_XUAT';
    if (mb_strpos($text, 'hết') !== false) return 'CANH_BAO';

    return 'UNKNOWN';
}
date_default_timezone_set('Asia/Ho_Chi_Minh');
$intent = detectIntent($message);
$reply = '';

switch ($intent) {
    case 'XIN_CHAO':
        $reply = 'Tôi có thể giúp gì cho bạn...!';
        break;
    case 'MET_QUA':
        $reply = 'Kệ m...!';
        break;   
    case 'TON_KHO':
        $reply = '📦 Tôi đang kiểm tra tồn kho cho bạn...';
        break;

    case 'PHIEU_NHAP':
        $reply = '🧾 Bạn muốn xem phiếu nhập theo ngày hay theo nhà cung cấp?';
        break;

    case 'PHIEU_XUAT':
        $reply = '📤 Bạn muốn xem phiếu xuất hôm nay hay toàn bộ?';
        break;

    case 'CANH_BAO':
        $reply = '⚠️ Tôi sẽ kiểm tra các mặt hàng sắp hết.';
        break;

    case 'NGAY_HOM_NAY':
        $reply = '📅 Hôm nay là ngày ' . date('d/m/Y');
        break;

    case 'GIO_HIEN_TAI':
        $reply = '⏰ Bây giờ là ' . date('H:i:s');
        break;
        
    default:
        $reply = '🤖 Tôi chưa hiểu rõ. Bạn có thể hỏi về tồn kho, phiếu nhập, phiếu xuất.';
}

echo json_encode(['reply' => $reply]);
