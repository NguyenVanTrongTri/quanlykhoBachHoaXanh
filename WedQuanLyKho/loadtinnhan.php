<?php
session_start();
include 'KetNoi/connect.php';
$conn = connectdb();

header('Content-Type: application/json; charset=utf-8');

/* ===== 0. CHECK SESSION ===== */
if (!isset($_SESSION['MaTK'])) {
    http_response_code(401);
    echo json_encode(['error' => 'NO_SESSION']);
    exit;
}
$today = date('Y-m-d');

if (!isset($_SESSION['NGAY_DON_TINNHAN']) 
    || $_SESSION['NGAY_DON_TINNHAN'] !== $today) {

    // DB lưu UTC → expireTime phải UTC
    $expireTimeUTC = (new DateTime('-2 days', new DateTimeZone('Asia/Ho_Chi_Minh')))
        ->setTimezone(new DateTimeZone('UTC'))
        ->format('Y-m-d H:i:s');

    $conn->beginTransaction();

    /* 1️⃣ XOÁ TIN NHẮN ĐÃ XEM (PHỤ) */
    $conn->prepare("
        DELETE FROM tinnhan_daxem
        WHERE MaTinNhan IN (
            SELECT MaTinNhan
            FROM tinnhan
            WHERE ThoiGian < ?
        )
    ")->execute([$expireTimeUTC]);

    /* 2️⃣ XOÁ TIN NHẮN (CHÍNH) */
    $conn->prepare("
        DELETE FROM tinnhan
        WHERE ThoiGian < ?
    ")->execute([$expireTimeUTC]);

    /* 3️⃣ XOÁ HỘI THOẠI RỖNG */
    $conn->prepare("
        DELETE FROM cuochoithoai
        WHERE MaCHT NOT IN (
            SELECT DISTINCT MaCHT FROM tinnhan
        )
        AND ThoiGianTao < ?
    ")->execute([$expireTimeUTC]);

    $conn->commit();

    $_SESSION['NGAY_DON_TINNHAN'] = $today;
}



$MaTK = $_SESSION['MaTK'];

/* ===== 1. LẤY HỘI THOẠI HÔM NAY ===== */
$stmtCHT = $conn->prepare("
  SELECT MaCHT
FROM cuochoithoai
WHERE ThoiGianTao >= CURDATE() - INTERVAL 1 DAY
ORDER BY ThoiGianTao DESC
LIMIT 1;
");
$stmtCHT->execute();

$MaCHT = $stmtCHT->fetchColumn();

if (!$MaCHT) {
    // Hôm nay chưa có hội thoại → chưa có tin
    echo json_encode([]);
    exit;
}
/* ===== 3. ĐÁNH DẤU ĐÃ XEM ===== */
$stmtSeen = $conn->prepare("
    INSERT IGNORE INTO tinnhan_daxem (MaTinNhan, MaTK)
    SELECT tn.MaTinNhan, ?
    FROM tinnhan tn
    WHERE tn.MaCHT = ?
      AND tn.MaTK_Gui <> ?
");
$stmtSeen->execute([$MaTK, $MaCHT, $MaTK]);

/* ===== 4. LOAD TIN NHẮN ===== */
$stmt = $conn->prepare("
    SELECT 
        tn.MaTinNhan,
        tn.MaTK_Gui,
        tn.NoiDung,
        tn.ThoiGian,
        nv.HoTen,
        tn.MaCHT
    FROM tinnhan tn
    JOIN cuochoithoai cht ON tn.MaCHT = cht.MaCHT
    JOIN taikhoan tk ON tn.MaTK_Gui = tk.MaTK
    JOIN nhanvien nv ON tk.MaNV = nv.MaNV
    WHERE cht.ThoiGianTao >= CURDATE() - INTERVAL 1 DAY
    ORDER BY tn.MaCHT, tn.ThoiGian ASC;
");
$stmt->execute();

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
