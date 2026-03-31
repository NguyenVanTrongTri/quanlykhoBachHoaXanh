<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include 'KetNoi/connect.php';
$conn = connectdb();

$MaTK = $_SESSION['MaTK'];
$data = json_decode(file_get_contents("php://input"), true);
$NoiDung = trim($data['NoiDung'] ?? '');

if ($NoiDung === '') {
    echo json_encode(['error' => 'EMPTY_MESSAGE']);
    exit;
}

try {
    $conn->beginTransaction();

    /* 1️⃣ TÌM HỘI THOẠI HÔM NAY */
    $sqlFindCHT = "
        SELECT MaCHT
        FROM cuochoithoai
        WHERE DATE(ThoiGianTao) = CURDATE()
        ORDER BY MaCHT DESC
        LIMIT 1
    ";
    $stmtFind = $conn->prepare($sqlFindCHT);
    $stmtFind->execute();

    if ($row = $stmtFind->fetch(PDO::FETCH_ASSOC)) {
        $MaCHT = $row['MaCHT'];
    } else {
        /* 2️⃣ CHƯA CÓ → TẠO HỘI THOẠI MỚI */
        $sqlCreate = "
            INSERT INTO cuochoithoai (TenCHT, LaNhom, ThoiGianTao)
            VALUES (?, 0, NOW())
        ";
        $stmtCreate = $conn->prepare($sqlCreate);
        $stmtCreate->execute(['Chat ' . date('d/m/Y')]);

        $MaCHT = $conn->lastInsertId();
    }

    /* 3️⃣ THÊM USER VÀO HỘI THOẠI (NẾU CHƯA CÓ) */
    $sqlJoin = "
        INSERT IGNORE INTO cuochoithoai_thanhvien (MaCHT, MaTK)
        VALUES (?, ?)
    ";
    $stmtJoin = $conn->prepare($sqlJoin);
    $stmtJoin->execute([$MaCHT, $MaTK]);

    /* 4️⃣ GHI TIN NHẮN */
    $sqlMsg = "
        INSERT INTO tinnhan (MaCHT, MaTK_Gui, NoiDung)
        VALUES (?, ?, ?)
    ";
    $stmtMsg = $conn->prepare($sqlMsg);
    $stmtMsg->execute([$MaCHT, $MaTK, $NoiDung]);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'MaCHT'   => $MaCHT
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['error' => 'SERVER_ERROR']);
}
