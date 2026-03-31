<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

function connectdb() {
    $servername = "db-bach-hoa-xanh-trongtri14780-2a54.j.aivencloud.com";
    $port = "16063"; 
    $username = "avnadmin";
    $password = "AVNS_0jfSoEV9FoIcW9MljSE"; 
    $dbname = "db_quanlykho"; 

    try {
        $dsn = "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4";
        
        // Cấu hình BẮT BUỘC cho Aiven và Render
         $options = [
            PDO::MYSQL_ATTR_SSL_CA => __DIR__ . "/ca.pem", // file CA tải từ Aiven
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 30
        ];

        // CHỈ DÙNG 1 LỆNH DUY NHẤT DƯỚI ĐÂY
        $conn = new PDO($dsn, $username, $password, $options);

        $conn->exec("SET NAMES 'utf8mb4'");
        $conn->exec("SET time_zone = '+07:00'");

        // PHẢI CÓ DÒNG NÀY để ném kết nối ra ngoài cho nhóm dùng
        return $conn;

    } catch (PDOException $e) {
        // Dùng die để dừng chương trình ngay khi lỗi, dễ debug hơn exit
        die("Kết nối thất bại rồi Trọng ơi! Lỗi: " . $e->getMessage());
    }
}
?>
