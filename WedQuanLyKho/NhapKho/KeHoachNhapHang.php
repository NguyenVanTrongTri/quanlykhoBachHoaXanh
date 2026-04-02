<?php 
session_start();
include '../KetNoi/connect.php'; 
$conn = connectdb();

$defaultAvatar = 'default.png'; // chỉ tên file mặc định trong uploads/avatar
$avatar = $defaultAvatar;       // mặc định
$tenNguoiDung = 'Khách';
$chucVu = null;
if (isset($_SESSION['MaTK'])) {
    $sql = "SELECT tk.TenDangNhap, tk.Avatar, nv.ChucVu
            FROM taikhoan tk
            LEFT JOIN nhanvien nv ON nv.MaNV = tk.MaNV
            WHERE tk.MaTK = ?
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$_SESSION['MaTK']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $tenNguoiDung = $row['TenDangNhap'] ?: 'Khách';
        // nếu Avatar trống → dùng default.png
        $avatar = !empty($row['Avatar']) ? $row['Avatar'] : $defaultAvatar;
        $chucVu = $row['ChucVu'];
    }
}
// đường dẫn đầy đủ cho HTML
$avatarPath = "../uploads/avatar/$avatar";

function getChucVu($maTK) {
    global $conn; // dùng PDO connection từ connect.php

    $sql = "SELECT nv.ChucVu 
            FROM taikhoan tk
            INNER JOIN nhanvien nv ON nv.MaNV = tk.MaNV
            WHERE tk.MaTK = ? 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$maTK]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['ChucVu'] : null;
} 
$chucVu = null;
if (isset($_SESSION['MaTK'])) {
    $chucVu = getChucVu($_SESSION['MaTK']);
} 
function getLichSuNhapTuMoc() {
    $conn = connectdb();
    // Truy vấn tổng số lượng thực tế nhập theo từng ngày
    // Lấy từ bảng phieunhap (để có ThoiGian) và chitietphieunhap (để có SoLuongThucTeNhap)
    $sql = "SELECT DATE(pn.ThoiGian) as Ngay, SUM(ct.SoLuongThucTeNhap) as TongQty 
            FROM phieunhap pn
            INNER JOIN chitietphieunhap ct ON pn.MaPhieuNhap = ct.MaPhieuNhap
            WHERE DATE(pn.ThoiGian) >= '2025-09-25' 
              AND DATE(pn.ThoiGian) <= CURDATE()
            GROUP BY DATE(pn.ThoiGian)
            ORDER BY Ngay ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$lichSuData = getLichSuNhapTuMoc();

function getLichSuChiTietTungHang() {
    $conn = connectdb();
    $sql = "SELECT 
                DATE(pn.ThoiGian) as Ngay, 
                ct.MaHangHoa, 
                SUM(ct.SoLuongThucTeNhap) as Qty
            FROM phieunhap pn
            INNER JOIN chitietphieunhap ct ON pn.MaPhieuNhap = ct.MaPhieuNhap
            WHERE DATE(pn.ThoiGian) >= '2025-09-25' 
              AND DATE(pn.ThoiGian) <= CURDATE()
            GROUP BY DATE(pn.ThoiGian), ct.MaHangHoa
            ORDER BY Ngay ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Chuyển dữ liệu này sang JS
$lichSuChiTiet = getLichSuChiTietTungHang();
?>
<?php define('ALLOW_RENDER', true);
include_once('../ThongTinTaiKhoan.php');
include_once __DIR__ . '/../Tienich.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trang chủ | Bách Hóa Xanh</title>
<style>
/* Hiệu ứng chọn dòng*/
.active-row {
    background: #d1eaff !important;
    transition: 0.2s;
}
.row-select:hover {
    background: #f0f8ff;
    cursor: pointer;
}
/* Dùng cho tim kiém*/
    .search-area {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 10px;
    background-color: #e5e5e5;
}

.search-input {
    width: 150px;
    height: 30px;
    border-radius: 6px;
    background-color: white;
    border: 1px solid #ccc;
    margin-right: 10px;
}

.btn {
    padding: 8px 15px;
    border: none;
    cursor: pointer;
    font-weight: bold;
    border-radius: 4px;
}

.btn-search {
    border-radius: 6px;
    background-color: #e74c3c; /* Màu đỏ */
    color: white;
}

    /* FORM CONTENT STYLES */
    .form-container {
        padding: 20px;
    }


    .form-title {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 20px;
    }

    .section-box {
        background: #f0f0f0; /* Màu nền xám nhạt cho từng phần */
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .section-title {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 15px;
    }

    .form-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 30px; /* Khoảng cách giữa các cặp label/input */
        margin-bottom: 10px;
    }

    .form-field {
        display: flex;
        align-items: center;
        width: calc(50% - 15px); /* Chia 2 cột, trừ khoảng gap */
    }
    .form-field label {
        width: 150px; /* Chiều rộng cố định cho label */
        font-weight: 50;
        color: #555;
        flex-shrink: 0;
    }
    .form-field input[type="text"] {
        flex-grow: 1;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }
    .form-field input[type="number"] {
    width: 450px; /* chỉnh chiều rộng tùy ý */
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    }   
    .form-select {
    flex-grow: 5;          /* cho phép giãn giống input */
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;

    /* Tùy chỉnh kích thước */
    width: 500%;           /* bạn có thể đổi 150px, 200px, 50%... */
    max-width: 550px;      /* muốn giới hạn chiều rộng */
    }

    .form-field button {
        padding: 8px 15px;
        border: none;
        background: #00923F;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 10px;
        transition: 0.2s;
    }

    .form-field button:hover {
        background: #007b36;
    }

    /* TABLE STYLES (FOOTER TABLE) */
    .data-table-container {
        margin-top: 20px;
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .data-table th, .data-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
        font-size: 10px;
    }

    .data-table th {
        background-color: #00923F;
        color: white;
        font-weight: bold;
    }

    .data-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .data-table tr:hover {
        background-color: #f1f1f1;
    }

    /* BUTTONS */
    .action-buttons {
        display: flex;
        justify-content: flex-end; /* Căn nút sang phải */
        gap: 15px;
        padding: 15px 0;
    }

    .action-buttons .btn {
        padding: 10px 25px;
        font-size: 10px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
    }

    .action-buttons .btn.edit {
        background: #e60000; /* Màu đỏ */
        color: white;
    }

    .action-buttons .btn.delete {
        background: #00923F; /* Màu xanh lá */
        color: white;
    }
    .action-buttons .btn.clear {
        background: #00923F; /* Màu xanh lá */
        color: white;
    }

    .action-buttons .btn.add:hover {
        background: #cc0000;
    }

    .action-buttons .btn.delete:hover {
        background: #007b36;
    }
    /* FOOTER SUMMARY & ACTIONS */
    .summary-and-actions {
        padding: 20px 0;
    }

    .summary-info {
        display: flex;
        justify-content: flex-start;
        gap: 15px;
        margin-bottom: 25px;
    }

    .summary-field {
        font-size: 16px;
        font-weight: bold;
        color: #333;
        display: flex;
        align-items: center;
    }

    .summary-field input {
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        font-weight: bold;
        margin-left: 10px;
        width: 200px; /* Chiều rộng cho trường tổng tiền */
        text-align: right;
        background: #fff;
    }

    .final-actions {
        display: flex;
        justify-content: flex-end; /* Đẩy các nút sang phải */
        gap: 15px;
    }

    .final-actions .btn {
        padding: 12px 30px;
        font-size: 10px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
    }

    .final-actions .btn.save {
        background: #e60000; /* Màu đỏ */
        color: white;
    }

    .final-actions .btn.cancel {
        background: #00923F; /* Màu xanh lá */
        color: white;
    }

    .final-actions .btn.save:hover {
        background: #cc0000;
    }

    .final-actions .btn.cancel:hover {
        background: #007b36;
    }
         /* AVARTA */
    .user-image-wrapper {
    width: 42px;
    height: 42px;
    border-radius: 50%;    /* avatar tròn - có thể đổi */
    overflow: hidden;      /* cắt phần thừa */
    flex-shrink: 0;        /* không bị co lại khi thu sidebar */
    }
	.user-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    }
    .user-image {
        width: 100%;
        height: 100%;
        object-fit: cover;     /* căn chỉnh ảnh cho đẹp */
    }
</style>
</head>

<body>
<?php
$path = "../";
include(__DIR__ . "/../php/header.php");
include('../ThongTinTaiKhoan.php'); 
?>
<script>
// chức năng click tên để mở cửa sổ thông tin tài khoảng
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.username').addEventListener('click', function() {
        fetch('../ThongTinTaiKhoan.php')
            .then(response => response.text())
            .then(html => {
                document.body.insertAdjacentHTML('beforeend', html);
                document.getElementById('modalOverlay').style.display = 'block';
                document.getElementById('userModal').style.display = 'block';
            });
    });
});
</script>
<div class="layout">
    <div class="sidebar">
        <div class="user-box">
            <div class="user-image-wrapper">
                <img src="<?= $avatarPath ?>" class="user-image" alt="User Avatar">
            </div>
            <div class="username">
                <?php echo htmlspecialchars($tenNguoiDung); ?>
            </div>
        </div>

        <button class="overview-btn" onclick="window.location.href='../TrangChu.php'" >Tổng quan</button>

        <!-- NHẬP KHO -->
      <script>
        // Truyền biến chức vụ sang JS
        window.userChucVu = '<?php echo $chucVu; ?>';
        </script>
       <script src="../php/phanquyen.js?v=<?= time() ?>"></script> <!-- đường dẫn tới file JS dùng chung -->
        <!-- NHẬP KHO -->
       <div class="menu-item parent">📥 Nhập kho</div>
        <div class="menu-item" onclick="checkPermission('../NhapKho/TaoPhieuNhapKho.php')">> Tạo phiếu nhập</div>
        <div class="menu-item" onclick="checkPermission('../NhapKho/CapNhatPhieuNhapKho.php')">> Cập nhật phiếu nhập</div>
        <div class="menu-item" onclick="checkPermission('../NhapKho/ThongKePhieuNhapKho.php')">> Thống kê phiếu nhập</div>
        <div class="menu-item" onclick="checkPermission('../NhapKho/KeHoachNhapHang.php')">> Kế hoạch nhập hàng</div>

        <!-- XUẤT KHO -->
        <div class="menu-item parent">📤 Xuất kho</div>
        <div class="menu-item" onclick="checkPermission('../XuatKho/TaoPhieuXuat.php')">> Tạo phiếu xuất</div>
        <div class="menu-item" onclick="checkPermission('../XuatKho/CapNhatPhieuXuatKho.php')">> Cập nhật phiếu xuất</div>
        <div class="menu-item" onclick="checkPermission('../XuatKho/ThongKePhieuXuatKho.php')">> Thống kê phiếu xuất</div>

        <!-- TỒN KHO -->
        <div class="menu-item parent">📦 Tồn kho</div>
        <div class="menu-item" onclick="checkPermission('../TonKho/ThongKeHangTon.php')">> Thống kê hàng tồn</div>
        <div class="menu-item" onclick="checkPermission('../TonKho/DieuChinhHangTon.php')">> Điều chỉnh hàng tồn</div>
        <div class="menu-item" onclick="checkPermission('../TonKho/CanhBaoHangTon.php')">> Cảnh báo hàng tồn</div>
        <div class="menu-item" onclick="checkPermission('../TonKho/QuanLyKeHang.php')">> Quản lý kệ hàng</div>
        
        <!-- HÀNG HÓA -->
        <div class="menu-item parent">📦 Hàng hóa</div>
        <div class="menu-item" onclick="checkPermission('../HangHoa/ThemHangHoa.php')">> Thêm hàng hóa</div>
        <div class="menu-item" onclick="checkPermission('../HangHoa/CapNhatHangHoa.php')">> Cập nhật hàng hóa</div>
  
        <!-- NHÂN VIÊN -->
        <div class="menu-item parent">👥 Nhân viên</div>
        <div class="menu-item" onclick="checkPermission('../NhanVien/QuanLyNhanVien.php')">> Quản lý nhân viên</div>
        <div class="menu-item" onclick="checkPermission('../TaiKhoan/QuanLyTaiKhoan.php')">> Quản lý tài khoản</div>
        
    </div>
    <div class="content">
        <div class="form-container">
            <div class="form-title">📊 KẾ HOẠCH NHẬP HÀNG (DỰ BÁO AI)</div>
                <div class="section-box">
                    <div class="section-title">Cấu hình dự báo</div>
                    <div class="form-group" style="align-items: center;">
                        <div class="form-field">
                            <label>Chọn thời gian:</label>
                            <select id="forecastDays" class="form-select">
                                <option value="1">1 ngày tới</option>
                                <option value="2" selected>2 ngày tới</option>
                                <option value="3">3 ngày tới</option>
                                <option value="7">7 ngày tới</option>
                                <option value="30">30 ngày tới</option>
                            </select>
                        </div>
                        <button id="btnForecast" class="btn" 
                            style="background: #e60000; color: white; padding: 10px 25px; border-radius: 6px; min-width: 200px;">
                            🚀 Bắt đầu dự báo
                        </button>
                    </div>
                </div>

                <div class="section-box">
                    <div class="section-title">Biểu đồ dự báo số lượng nhập (Top hàng hóa)</div>
                    <canvas id="forecastChart" style="max-height: 400px;"></canvas>
                </div>

                <div class="section-box">
                    <div class="section-title">Chi tiết danh sách dự báo - Ngày: <span id="displayDate">--/--/----</span></div>
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Mã Hàng</th>
                                    <th>Tên Hàng Hóa</th>
                                    <th>ĐVT</th>
                                    <th>Số lượng dự báo (Qty)</th>
                                    <th>Độ chính xác</th>
                                </tr>
                            </thead>
                            <tbody id="forecastTableBody">
                                <tr>
                                    <td colspan="5" style="text-align: center;">Chưa có dữ liệu. Vui lòng bấm nút dự báo.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </div>
</div>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.0.1"></script>

<script>
// 1. Khởi tạo biến toàn cục
let myForecastChart = null;
const lichSuTong = <?php echo json_encode($lichSuData); ?>; 
const lichSuChiTiet = <?php echo json_encode($lichSuChiTiet); ?>; 
let duBaoHienTai = null; 

// MỐC THỜI GIAN CỐ ĐỊNH: 02/04/2026
const TODAY_STR = "2026-04-02";

window.onload = function() {
    renderTongQuat();
};

// 2. SỰ KIỆN BẤM NÚT DỰ BÁO (NÂNG CẤP)
document.getElementById('btnForecast').addEventListener('click', function() {
    const days = document.getElementById('forecastDays').value; // Lấy giá trị từ Select
    const btn = this;
    const originalContent = btn.innerHTML; 

    btn.innerText = '⏳ Đang tính toán...';
    btn.disabled = true;

    // Gọi API với số ngày đã chọn
    fetch(`https://lora-ai-9ti1.onrender.com/api/forecast?days=${days}`)
        .then(response => response.json())
        .then(res => {
            if (res.status === "success") {
                duBaoHienTai = res.data;
                renderDataTable(res.data); 
                renderTongQuat(); // Vẽ lại biểu đồ tổng với số ngày mới
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            btn.innerHTML = originalContent; 
            btn.disabled = false;
        });
});

// 3. HÀM VẼ BIỂU ĐỒ TỔNG (Sửa logic để hiển thị đúng số ngày chọn)
function renderTongQuat() {
    const chartLabels = [];
    const chartValues = [];

    // Nạp quá khứ
    lichSuTong.forEach(row => {
        chartLabels.push(row.Ngay);
        chartValues.push(parseFloat(row.TongQty));
    });

    // Ép mốc hiện tại
    if (!chartLabels.includes(TODAY_STR)) {
        chartLabels.push(TODAY_STR);
        chartValues.push(chartValues[chartValues.length - 1] || 0);
    }

    // Nếu đã có dữ liệu AI trả về
    if (duBaoHienTai) {
        const tongQtyDuBao = duBaoHienTai.items.reduce((sum, item) => sum + item.qty, 0);
        const days = parseInt(document.getElementById('forecastDays').value); // Đọc từ select
        
        // Tạo các mốc ngày tương lai trên trục X
        const lastDate = new Date(TODAY_STR);
        lastDate.setDate(lastDate.getDate() + days);
        
        chartLabels.push(lastDate.toISOString().split('T')[0]);
        chartValues.push(tongQtyDuBao);
    }

    updateProChart(chartLabels, chartValues, "Tổng sản lượng nhập kho");
}

// 4. HÀM VẼ CHI TIẾT KHI CLICK DÒNG (Sửa logic ngày)
function drawChartForItem(maHang, tenHang, qtyDuBao) {
    const chartLabels = [];
    const chartValues = [];
    const lichSuCuaHang = lichSuChiTiet.filter(row => row.MaHangHoa === maHang);
    
    lichSuCuaHang.forEach(row => {
        chartLabels.push(row.Ngay);
        chartValues.push(parseFloat(row.Qty));
    });

    if (!chartLabels.includes(TODAY_STR)) {
        chartLabels.push(TODAY_STR);
        chartValues.push(chartValues[chartValues.length - 1] || 0);
    }

    // Lấy số ngày từ Select để vẽ điểm cuối khớp với lựa chọn
    const days = parseInt(document.getElementById('forecastDays').value);
    const targetDate = new Date(TODAY_STR);
    targetDate.setDate(targetDate.getDate() + days);
    
    chartLabels.push(targetDate.toISOString().split('T')[0]);
    chartValues.push(qtyDuBao);

    updateProChart(chartLabels, chartValues, `Chi tiết nhập hàng: ${tenHang}`);
}

// 5. Hàm vẽ Core (Giữ nguyên logic hôm trước)
function updateProChart(labels, values, titleText) {
    const ctx = document.getElementById('forecastChart').getContext('2d');
    if (window.myForecastChart) { window.myForecastChart.destroy(); }

    let todayIndex = labels.indexOf(TODAY_STR);
    if (todayIndex === -1) todayIndex = labels.length - 2;

    window.myForecastChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: titleText,
                data: values,
                tension: 0.4,
                fill: true,
                pointRadius: (ctx) => (ctx.dataIndex === labels.length - 1 ? 10 : 4),
                pointBackgroundColor: (ctx) => (ctx.dataIndex === labels.length - 1 ? '#e60000' : '#00923F'),
                segment: {
                    borderColor: ctx => ctx.p1.idx > todayIndex ? '#e60000' : '#00923F',
                    borderDash: ctx => ctx.p1.idx > todayIndex ? [5, 5] : [], 
                }
            }]
        },
        options: {
            responsive: true,
            plugins: {
                annotation: {
                    annotations: {
                        todayLine: {
                            type: 'line', xMin: todayIndex, xMax: todayIndex,
                            borderColor: '#FF5722', borderWidth: 2, borderDash: [6, 4],
                            label: { display: true, content: 'HIỆN TẠI', position: 'end' }
                        }
                    }
                }
            },
            scales: { y: { beginAtZero: true } }
        }
    });
}

// Hàm render bảng (Nhớ giữ lại hàm này của bạn)
function renderDataTable(data) {
    const sortedItems = data.items.sort((a, b) => b.qty - a.qty);
    const tbody = document.getElementById('forecastTableBody');
    tbody.innerHTML = '';
    sortedItems.forEach(item => {
        tbody.innerHTML += `
            <tr onclick="drawChartForItem('${item.ma_hang}', '${item.ten_hang}', ${item.qty})" style="cursor:pointer">
                <td>${item.ma_hang}</td>
                <td>${item.ten_hang}</td>
                <td>${item.dvt}</td>
                <td><strong>${item.qty.toFixed(2)}</strong></td>
                <td>${duBaoHienTai.accuracy || 'N/A'}</td>
            </tr>`;
    });
}
</script>