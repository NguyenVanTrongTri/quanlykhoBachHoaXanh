function checkPermission(url) {
    // 1. Lấy dữ liệu từ window
    const allData = window.allDbPermissions || [];
    const userChucVu = (window.userChucVu || '').trim();

    // 2. Tự động đổ dữ liệu vào các mảng
    const nhapKho  = allData.filter(d => d.ChucNang === 'NhapKho').map(d => d.TenChucVu);
    const xuatKho  = allData.filter(d => d.ChucNang === 'XuatKho').map(d => d.TenChucVu);
    const tonKho   = allData.filter(d => d.ChucNang === 'TonKho').map(d => d.TenChucVu);
    const hanghoa  = allData.filter(d => d.ChucNang === 'HangHoa').map(d => d.TenChucVu);
    const nhanvien = allData.filter(d => d.ChucNang === 'NhanVien').map(d => d.TenChucVu);
    const taikhoan = allData.filter(d => d.ChucNang === 'TaiKhoan').map(d => d.TenChucVu);

    // 3. Xác định mảng "được phép"
    let allowed = [];
    if (url.includes('NhapKho')) allowed = nhapKho;
    else if (url.includes('XuatKho')) allowed = xuatKho;
    else if (url.includes('TonKho')) allowed = tonKho;
    else if (url.includes('HangHoa')) allowed = hanghoa;
    else if (url.includes('NhanVien')) allowed = nhanvien;
    else if (url.includes('TaiKhoan')) allowed = taikhoan;

    // 4. Kiểm tra
    const hasPermission = allowed.some(v => v.trim() === userChucVu);

    if (hasPermission) {
        window.location.href = url;
    } else {
        alert('Bạn không có quyền truy cập trang này!');
        console.log("Danh sách được phép vào mục này:", allowed);
    }
}