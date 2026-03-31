<?php
session_start();
header('Content-Type: application/json');

include 'KetNoi/connect.php';
$conn = connectdb();

if (!isset($_SESSION['MaTK'])) {
    echo json_encode(['unread' => 0]);
    exit;
}

$MaTK = $_SESSION['MaTK'];

/* 1️⃣ LẤY HỘI THOẠI GẦN NHẤT (HÔM NAY) */
$stmtCHT = $conn->prepare("
    SELECT MaCHT
    FROM cuochoithoai
    WHERE DATE(ThoiGianTao) = CURDATE()
    ORDER BY MaCHT DESC
    LIMIT 1
");
$stmtCHT->execute();
$MaCHT = $stmtCHT->fetchColumn();

if (!$MaCHT) {
    echo json_encode(['unread' => 0]);
    exit;
}

/* 2️⃣ ĐẾM UNREAD CHỈ TRONG CHT NÀY */
$sql = "
    SELECT COUNT(*) AS unread
    FROM tinnhan tn
    WHERE tn.MaCHT = :MaCHT
      AND tn.MaTK_Gui <> :MaTK
      AND NOT EXISTS (
          SELECT 1
          FROM tinnhan_daxem dx
          WHERE dx.MaTinNhan = tn.MaTinNhan
            AND dx.MaTK = :MaTK
      )
";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':MaTK'  => $MaTK,
    ':MaCHT'=> $MaCHT
]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
