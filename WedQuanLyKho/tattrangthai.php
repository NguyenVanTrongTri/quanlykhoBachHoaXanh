<?php
session_start();
header('Content-Type: application/json');

include 'KetNoi/connect.php';
$conn = connectdb();

/* test cứng */
$MaTK = $_SESSION['MaTK'];
$MaCHT = (int)($_GET['MaCHT'] ?? 0);

try {
    $sql = "
        INSERT INTO tinnhan_daxem (MaTinNhan, MaTK)
        SELECT tn.MaTinNhan, :MaTK
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
        ':MaCHT' => $MaCHT
    ]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['error' => 'SERVER_ERROR']);
}
