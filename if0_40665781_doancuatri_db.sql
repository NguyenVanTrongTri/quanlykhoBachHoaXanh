-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: sql200.byetcluster.com
-- Thời gian đã tạo: Th3 15, 2026 lúc 07:38 AM
-- Phiên bản máy phục vụ: 11.4.10-MariaDB
-- Phiên bản PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `if0_40665781_doancuatri_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieunhap`
--

CREATE TABLE `chitietphieunhap` (
  `STT` int(11) NOT NULL,
  `MaPhieuNhap` char(10) NOT NULL,
  `MaHangHoa` char(10) NOT NULL,
  `SoLuongTheoChungTu` int(11) DEFAULT NULL,
  `SoLuongThucTeNhap` int(11) DEFAULT NULL,
  `DonViTinh` varchar(50) NOT NULL,
  `DonGia` decimal(18,2) NOT NULL,
  `ThanhTien` decimal(18,2) GENERATED ALWAYS AS (`SoLuongThucTeNhap` * `DonGia`) STORED,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietphieunhap`
--

INSERT INTO `chitietphieunhap` (`STT`, `MaPhieuNhap`, `MaHangHoa`, `SoLuongTheoChungTu`, `SoLuongThucTeNhap`, `DonViTinh`, `DonGia`, `GhiChu`) VALUES
(171, 'PX002', 'hh005', 50, 50, 'Thùng', '7000.00', NULL),
(172, 'PX003', 'HH003', 5, 5, 'Thùng', '3000.00', NULL),
(173, 'PX004', 'hh005', 10, 10, 'Thùng', '2450000.00', NULL),
(174, 'PX005', 'hh006', 10, 10, 'Thùng', '6000.00', NULL),
(180, 'PX008', 'hh005', 10, 10, 'Thùng', '6000.00', NULL),
(181, 'PX008', 'HH002', 5, 5, 'Thùng', '6000.00', NULL),
(183, 'PX0001', 'HH001', 15, 15, 'Thùng', '3450000.00', NULL),
(184, 'PX0002', 'HH001', 35, 35, 'Thùng', '560000.00', NULL),
(185, 'pn0012', 'HH001', 15, 15, 'Thùng', '45000.00', NULL),
(186, 'pn0012', 'hh009', 15, 11, 'Thùng', '0.00', NULL),
(187, 'PN3101', 'HH009', 34, 34, 'Thùng', '340000.00', NULL),
(190, 'PN3201', 'HH001', 10, 10, 'Kg', '134000.00', NULL),
(191, 'PN3202', 'HH025', 11, 11, 'Thùng', '12000.00', NULL),
(192, 'PN3202', 'HH003', 34, 34, 'Thùng', '235000.00', NULL),
(194, 'PN00432', 'HH0056', 12, 12, '', '340000.00', NULL),
(195, 'PN00343', 'HH0056', 10, 10, '', '430000.00', NULL),
(196, 'PN00344', 'HH0056', 10, 10, 'Thùng', '430000.00', NULL),
(197, 'PN00345', 'HH0056', 12, 12, 'Thùng', '43000.00', NULL),
(198, 'PN00346', 'HH0056', 12, 12, 'Thùng', '43000.00', NULL),
(199, 'PN00347', 'HH0056', 5, 5, 'Thùng', '10000.00', NULL),
(200, 'PN00348', 'HH0056', 4, 4, 'Kg', '1000.00', NULL),
(201, 'PN00349', 'HH0056', 7, 7, 'Thùng', '10000.00', NULL),
(202, 'PN00350', 'HH0056', 10, 10, 'Thùng', '300000.00', NULL),
(203, 'PN00351', 'HH0056', 10, 10, 'Thùng', '30000.00', NULL),
(204, 'PN00352', 'HH0056', 5, 5, 'Hộp', '10000.00', NULL),
(205, 'PN0567', 'HH0056', 16, 16, 'Thùng', '50000.00', NULL),
(206, 'PN00355', 'HH0056', 10, 10, 'Thùng', '450000.00', NULL),
(207, 'PN00356', 'HH0056', 15, 15, 'Thùng', '25000.00', NULL),
(208, 'PN00358', 'HH0056', 10, 10, 'Thùng', '23000.00', NULL),
(209, 'PN00359', 'HH0056', 5, 5, 'Kg', '50000.00', NULL),
(210, 'PN00357', 'HH0056', 11, 11, 'Thùng', '230000.00', NULL),
(211, 'PN04035', 'HH0056', 10, 10, 'Thùng', '40000.00', NULL),
(212, 'PN04037', 'HH025', 16, 16, 'Thùng', '50000.00', NULL),
(213, 'PN040378', 'HH0056', 10, 10, 'Hộp', '56000.00', NULL),
(215, 'PN040376', 'HH031', 5, 5, 'Thùng', '560000.00', NULL),
(216, 'PN0403734', 'HH030', 12, 12, 'Thùng', '89000.00', NULL),
(217, 'PN04037390', 'HH030', 12, 12, 'Thùng', '89000.00', NULL),
(218, 'PN0431', 'HH0056', 10, 10, 'Thùng', '50000.00', NULL),
(219, 'PN0432', 'HH0056', 10, 10, 'Thùng', '40000.00', NULL),
(220, 'PN0434', 'HH0056', 15, 15, 'Thùng', '58000.00', NULL),
(221, 'PN0436', 'HH0056', 5, 5, 'Thùng', '10000.00', NULL),
(222, 'PN0438', 'HH0056', 10, 10, 'Thùng', '45000.00', NULL),
(223, 'PN0439', 'HH0056', 11, 11, 'Thùng', '13000.00', NULL),
(224, 'PN04390', 'HH0056', 12, 12, 'Thùng', '14000.00', NULL),
(225, 'PN043954', 'HH0056', 10, 10, 'Thùng', '14000.00', NULL),
(226, 'PN043955', 'HH0056', 8, 8, 'Thùng', '34000.00', NULL),
(227, 'PN043958', 'HH031', 15, 15, 'Thùng', '45000.00', NULL),
(228, 'PN043958', 'HH010', 12, 12, 'Thùng', '22000.00', NULL),
(229, 'PN043956', 'HH0056', 12, 12, 'Thùng', '50000.00', NULL),
(230, 'PN0439532', 'HH0056', 23, 23, 'Thùng', '5000.00', NULL),
(231, 'PN04395322', 'HH0056', 9, 9, 'Thùng', '40000.00', NULL),
(232, 'PN043212', 'HH021', 14, 14, 'Kg', '20000.00', NULL),
(233, 'PN0432121', 'HH027', 14, 14, 'Kg', '20000.00', NULL),
(234, 'PN04321212', 'HH008', 12, 12, 'Thùng', '40000.00', NULL),
(235, 'PN043232', 'HH029', 12, 12, 'Kg', '40000.00', NULL),
(236, 'PN04323212', 'HH026', 11, 11, 'Thùng', '34000.00', NULL),
(237, 'PN435', 'HH027', 23, 23, 'Kg', '12000.00', NULL),
(238, 'PN435', 'HH019', 12, 12, 'Thùng', '34000.00', NULL),
(239, 'PN435', 'HH008', 5, 5, 'Thùng', '80000.00', NULL),
(240, 'PN04037621', 'HH025', 12, 12, 'Thùng', '43000.00', NULL),
(241, 'PN04037621', 'HH006', 3, 3, 'Thùng', '120000.00', NULL),
(242, 'PN04037621', 'HH005', 6, 6, 'Thùng', '340000.00', NULL),
(243, 'PN4522', 'HH018', 6, 6, 'Thùng', '45000.00', NULL),
(244, 'PN4522', 'HH017', 3, 3, 'Thùng', '23000.00', NULL),
(245, 'PN4522', 'HH019', 7, 7, 'Thùng', '35000.00', NULL),
(246, 'PN4523', 'HH015', 12, 12, 'Thùng', '45000.00', NULL),
(247, 'PN4523', 'HH014', 4, 4, 'Thùng', '13200.00', NULL),
(248, 'PN4523', 'HH016', 23, 23, 'Thùng', '22000.00', NULL),
(249, 'PN4525', 'HH002', 5, 5, 'Kg', '150000.00', NULL),
(250, 'PN4525', 'HH001', 12, 12, 'Kg', '134000.00', NULL),
(251, 'PN4525', 'HH011', 17, 17, 'Kg', '45000.00', NULL),
(252, 'PN4525', 'HH013', 9, 9, 'Kg', '60000.00', NULL),
(253, 'PN45232', 'HH010', 120, 120, 'Thùng', '230000.00', NULL),
(254, 'PN45232', 'HH008', 50, 50, 'Thùng', '140000.00', NULL),
(255, 'PN45232', 'HH0087', 23, 23, 'Thùng', '360000.00', NULL),
(256, 'PN004', 'HH014', 2, 0, 'Thùng', '540000.00', NULL),
(257, 'PN004', 'HH025', 23, 23, 'Kg', '230000.00', NULL),
(258, 'PN0041', 'HH014', 3, 3, 'Thùng', '230000.00', NULL),
(259, 'PN0041', 'HH006', 1, 1, 'Thùng', '45000.00', NULL),
(260, 'PN0042', 'HH0087', 21, 21, 'Thùng', '230000.00', NULL),
(261, 'PN0042', 'HH020', 3, 3, 'Kg', '340000.00', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieuxuat`
--

CREATE TABLE `chitietphieuxuat` (
  `STT` int(11) NOT NULL,
  `MaPhieuXuat` char(10) NOT NULL,
  `MaHangHoa` char(10) NOT NULL,
  `SoLuongTheoChungTu` int(11) NOT NULL,
  `SoLuongThucTeXuat` int(11) NOT NULL,
  `DonViTinh` varchar(50) NOT NULL,
  `DonGia` decimal(18,2) NOT NULL,
  `ThanhTien` decimal(18,2) GENERATED ALWAYS AS (`SoLuongThucTeXuat` * `DonGia`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `chitietphieuxuat`
--

INSERT INTO `chitietphieuxuat` (`STT`, `MaPhieuXuat`, `MaHangHoa`, `SoLuongTheoChungTu`, `SoLuongThucTeXuat`, `DonViTinh`, `DonGia`) VALUES
(1, 'PX001', 'HH001', 50, 50, 'kg', '120000.00'),
(2, 'PX001', 'HH022', 30, 30, 'kg', '40000.00'),
(3, 'PX002', 'HH002', 20, 20, 'kg', '250000.00'),
(4, 'PX002', 'HH023', 25, 25, 'kg', '30000.00'),
(5, 'PX003', 'HH003', 15, 15, 'kg', '250000.00'),
(6, 'PX003', 'HH024', 40, 40, 'kg', '45000.00'),
(7, 'PX004', 'HH004', 10, 10, 'kg', '300000.00'),
(8, 'PX004', 'HH025', 50, 50, 'kg', '20000.00'),
(9, 'PX005', 'HH005', 5, 5, 'thùng', '180000.00'),
(10, 'PX005', 'HH006', 10, 10, 'thùng', '150000.00'),
(20, 'px0013', 'hh025', 10, 10, '', '3000.00'),
(21, 'PX008', 'HH025', 1, 1, '', '3000.00'),
(22, 'PX009', 'hh025', 2, 2, '', '4000.00'),
(23, 'PX010', 'HH001', 20, 20, '', '300000.00'),
(24, 'PX0020', 'HH001', 20, 20, '', '300000.00'),
(25, 'PX0011', 'HH001', 15, 15, '', '7000.00'),
(26, 'PX0010', 'HH005', 55, 55, 'Thùng', '104000.00'),
(27, 'px0012', 'HH001', 45, 45, 'Thùng', '450000.00'),
(28, 'PX0014', 'HH001', 15, 15, 'Kg', '1456000.00'),
(29, 'PX0015', 'HH001', 15, 15, '', '1456000.00'),
(30, 'PX0056', 'HH001', 15, 15, 'Kg', '543000.00'),
(31, 'px0023', 'HH001', 10, 10, '', '56000.00'),
(32, 'px0024', 'HH001', 12, 12, 'Thùng', '123000.00'),
(33, 'PX0657', 'HH0056', 30, 30, 'Thùng', '100000.00'),
(34, 'px0025', 'HH0056', 10, 10, 'Thùng', '50000.00'),
(36, 'px0026', 'HH0056', 3, 3, '', '50000.00'),
(37, 'px0027', 'HH0056', 3, 3, 'Thùng', '45000.00'),
(38, 'px0028', 'HH0056', 10, 10, 'Thùng', '50000.00'),
(39, 'PX00886', 'HH0056', 5, 5, 'Thùng', '45000.00'),
(40, 'PX00880', 'HH0056', 12, 12, 'Thùng', '560000.00'),
(41, 'PX00898', 'HH030', 10, 0, 'Thùng', '400000.00'),
(42, 'PX00234', 'HH026', 10, 10, 'Thùng', '34000.00'),
(43, 'PX0023412', 'HH008', 3, 3, 'Thùng', '34000.00'),
(44, 'PX323', 'HH019', 12, 12, 'Thùng', '4000.00'),
(45, 'PX00212', 'HH008', 2, 2, 'Thùng', '4000.00'),
(46, 'PX001124', 'HH005', 5, 5, 'Thùng', '430000.00'),
(47, 'PX001124', 'HH006', 3, 3, 'Thùng', '430000.00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cuochoithoai`
--

CREATE TABLE `cuochoithoai` (
  `MaCHT` int(11) NOT NULL,
  `TenCHT` varchar(200) DEFAULT NULL,
  `LaNhom` tinyint(1) DEFAULT 1,
  `ThoiGianTao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `cuochoithoai`
--

INSERT INTO `cuochoithoai` (`MaCHT`, `TenCHT`, `LaNhom`, `ThoiGianTao`) VALUES
(17, 'Chat 14/03/2026', 0, '2026-03-14 09:58:11'),
(18, 'Chat 15/03/2026', 0, '2026-03-15 18:06:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cuochoithoai_thanhvien`
--

CREATE TABLE `cuochoithoai_thanhvien` (
  `MaCHT` int(11) NOT NULL,
  `MaTK` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `cuochoithoai_thanhvien`
--

INSERT INTO `cuochoithoai_thanhvien` (`MaCHT`, `MaTK`) VALUES
(5, 'TK002'),
(6, 'TK002'),
(7, 'TK002'),
(8, 'TK002'),
(9, 'TK002'),
(10, 'TK002'),
(11, 'TK002'),
(12, 'TK002'),
(13, 'TK002'),
(15, 'TK002'),
(16, 'TK002'),
(18, 'TK002'),
(4, 'TK003'),
(5, 'TK003'),
(7, 'TK003'),
(9, 'TK003'),
(16, 'TK003'),
(4, 'TK005'),
(5, 'TK005'),
(8, 'TK005'),
(9, 'TK005'),
(15, 'TK005'),
(16, 'TK005'),
(4, 'TK007'),
(5, 'TK007'),
(6, 'TK007'),
(7, 'TK007'),
(8, 'TK007'),
(9, 'TK007'),
(10, 'TK007'),
(12, 'TK007'),
(13, 'TK007'),
(14, 'TK007'),
(16, 'TK007'),
(17, 'TK007'),
(5, 'TK008'),
(6, 'TK008'),
(7, 'TK008'),
(9, 'TK010'),
(4, 'TK012'),
(5, 'TK012'),
(6, 'TK012'),
(5, 'TK013'),
(7, 'TK013'),
(8, 'TK013'),
(9, 'TK013'),
(10, 'TK013'),
(9, 'TK014'),
(12, 'TK014'),
(15, 'TK014'),
(9, 'TK015'),
(11, 'TK015'),
(13, 'TK015'),
(15, 'TK015'),
(16, 'TK015');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc`
--

CREATE TABLE `danhmuc` (
  `STT` int(11) NOT NULL,
  `MaDanhMuc` char(10) NOT NULL,
  `MaNPP` char(10) DEFAULT NULL,
  `MaNCC` char(10) DEFAULT NULL,
  `MaLoaiDanhMuc` char(10) NOT NULL,
  `TenDanhMuc` varchar(255) NOT NULL,
  `LoaiDanhMuc` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc`
--

INSERT INTO `danhmuc` (`STT`, `MaDanhMuc`, `MaNPP`, `MaNCC`, `MaLoaiDanhMuc`, `TenDanhMuc`, `LoaiDanhMuc`, `MoTa`, `TrangThai`, `GhiChu`) VALUES
(1, 'DM01', 'NPP001', 'NCC001', 'LDM01', 'Thịt,cá,trứng,hải sản', 'Thực phẩm tươi sống', '', 'Hoạt động', ''),
(2, 'DM02', 'NPP002', 'NCC003', 'LDM01', 'Rau,củ,nấm,trái cây', 'Thực phẩm tươi sống', '', 'Hoạt động', ''),
(3, 'DM03', 'NPP003', 'NCC006', 'LDM02', 'Bia,nước giải khát', 'Thực phẩm chế biến & đồ uống', '', 'Hoạt động', ''),
(4, 'DM04', 'NPP001', 'NCC007', 'LDM02', 'Sữa các loại', 'Thực phẩm chế biến & đồ uống', '', 'Hoạt động', ''),
(5, 'DM05', 'NPP002', 'NCC009', 'LDM02', 'Gạo,bột,đồ khô', 'Thực phẩm chế biến & đồ uống', '', 'Hoạt động', ''),
(6, 'DM06', 'NPP003', 'NCC008', 'LDM02', 'Dầu ăn,nước chấm,gia vị', 'Thực phẩm chế biến & đồ uống', '', 'Hoạt động', ''),
(7, 'DM07', 'NPP019', 'NCC009', 'LDM02', 'Mì,miến,cháo,phở', 'Thực phẩm chế biến & đồ uống', '', 'Hoạt động', ''),
(8, 'DM08', 'NPP001', 'NCC007', 'LDM02', 'Kem,sữa chua', 'Thực phẩm chế biến & đồ uống', '', 'Hoạt động', ''),
(9, 'DM09', 'NPP004', 'NCC002', 'LDM02', 'Thực phẩm đông mát', 'Thực phẩm chế biến & đồ uống', '', 'Hoạt động', ''),
(10, 'DM10', 'NPP002', 'NCC009', 'LDM02', 'Bánh kẹo các loại', 'Thực phẩm chế biến & đồ uống', '', 'Hoạt động', ''),
(11, 'DM11', 'NPP010', 'NCC010', 'LDM03', 'Chăm sóc cá nhân', 'Sản phẩm chăm sóc cá nhân & gia đình', '', 'Hoạt động', ''),
(12, 'DM12', 'NPP010', 'NCC010', 'LDM03', 'Sản phẩm cho mẹ và bé', 'Sản phẩm chăm sóc cá nhân & gia đình', '', 'Hoạt động', ''),
(13, 'DM13', 'NPP011', 'NCC010', 'LDM03', 'Đồ dùng gia đình', 'Sản phẩm chăm sóc cá nhân & gia đình', '', 'Hoạt động', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donvivanchuyen`
--

CREATE TABLE `donvivanchuyen` (
  `STT` int(11) NOT NULL,
  `MaDonViVanChuyen` char(10) NOT NULL,
  `TenDonVi` varchar(100) NOT NULL,
  `NguoiVanChuyen` varchar(100) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `SDT` varchar(15) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `BienSoXe` varchar(50) NOT NULL,
  `LoaiXe` varchar(50) DEFAULT NULL,
  `MaNCC` char(10) NOT NULL,
  `MaNPP` char(10) NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `donvivanchuyen`
--

INSERT INTO `donvivanchuyen` (`STT`, `MaDonViVanChuyen`, `TenDonVi`, `NguoiVanChuyen`, `DiaChi`, `SDT`, `Email`, `BienSoXe`, `LoaiXe`, `MaNCC`, `MaNPP`, `GhiChu`) VALUES
(1, 'DV001', 'Vận chuyển An Phát', 'Nguyễn Văn A', '123 Lê Lợi, Q1, TP.HCM', '0901234561', 'anphat1@gmail.com', '51C-12345', 'Xe tải', 'NCC001', 'NPP001', 'Chuyên thịt heo, thịt bò'),
(2, 'DV002', 'Vận chuyển Hoàng Gia', 'Trần Thị B', '456 Trần Hưng Đạo, Q2, TP.HCM', '0901234562', 'hoanggia2@gmail.com', '51C-23456', 'Xe tải', 'NCC002', 'NPP002', 'Chuyên cá, hải sản'),
(3, 'DV003', 'Vận chuyển Hồng Phát', 'Lê Văn C', '789 Nguyễn Huệ, Q3, TP.HCM', '0901234563', 'hongphat3@gmail.com', '51C-34567', 'Xe tải', 'NCC003', 'NPP003', 'Chuyên rau củ quả'),
(4, 'DV004', 'Vận chuyển Minh Tâm', 'Phạm Thị D', '101 Lý Tự Trọng, Q4, TP.HCM', '0901234564', 'minhtam4@gmail.com', '51C-45678', 'Xe tải', 'NCC004', 'NPP004', 'Chuyên trứng'),
(5, 'DV005', 'Vận chuyển Thành Công', 'Nguyễn Văn E', '202 Pasteur, Q5, TP.HCM', '0901234565', 'thanhcong5@gmail.com', '51C-56789', 'Xe tải', 'NCC005', 'NPP005', 'Chuyên nấm, hoa tươi'),
(6, 'DV006', 'Vận chuyển Phát Lộc', 'Trần Văn F', '303 Võ Văn Kiệt, Q6, TP.HCM', '0901234566', 'phatloc6@gmail.com', '51C-67890', 'Xe tải', 'NCC006', 'NPP006', 'Chuyên bia, nước ngọt'),
(7, 'DV007', 'Vận chuyển Bách Khoa', 'Lê Thị G', '404 Nguyễn Tri Phương, Q7, TP.HCM', '0901234567', 'bachkhoa7@gmail.com', '51C-78901', 'Xe tải', 'NCC007', 'NPP007', 'Chuyên sữa, kem'),
(8, 'DV008', 'Vận chuyển Đại Phát', 'Phạm Văn H', '505 Hai Bà Trưng, Q8, TP.HCM', '0901234568', 'daiphat8@gmail.com', '51C-89012', 'Xe tải', 'NCC008', 'NPP008', 'Chuyên dầu ăn, gia vị'),
(9, 'DV009', 'Vận chuyển An Khang', 'Nguyễn Thị I', '606 Cách Mạng Tháng 8, Q1, TP.HCM', '0901234569', 'ankhang9@gmail.com', '51C-90123', 'Xe tải', 'NCC009', 'NPP009', 'Chuyên bánh kẹo'),
(10, 'DV010', 'Vận chuyển Thịnh Vượng', 'Trần Văn J', '707 Lê Lai, Q2, TP.HCM', '0901234570', 'thinhvuong10@gmail.com', '51C-01234', 'Xe tải', 'NCC010', 'NPP010', 'Chuyên đồ dùng gia đình');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hanghoa`
--

CREATE TABLE `hanghoa` (
  `STT` int(11) NOT NULL,
  `MaHangHoa` char(10) NOT NULL,
  `TenHangHoa` varchar(100) NOT NULL,
  `DonViTinh` varchar(50) DEFAULT NULL,
  `DonGia` decimal(18,2) DEFAULT NULL,
  `MaLoaiHang` char(10) NOT NULL,
  `NgaySanXuat` date NOT NULL,
  `HanSuDung` date NOT NULL,
  `XuatXu` varchar(100) DEFAULT NULL,
  `MaVach` varchar(50) NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `hanghoa`
--

INSERT INTO `hanghoa` (`STT`, `MaHangHoa`, `TenHangHoa`, `DonViTinh`, `DonGia`, `MaLoaiHang`, `NgaySanXuat`, `HanSuDung`, `XuatXu`, `MaVach`, `GhiChu`) VALUES
(1, 'HH001', 'Thịt heo', 'Kg', '120000.00', 'LHH01', '2026-02-02', '2026-05-28', 'Việt Nam', '8934567890123', ''),
(2, 'HH002', 'Thịt bò', 'Kg', '250000.00', 'LHH01', '2026-02-05', '2026-09-17', 'Na Uy', '8934567890124', ''),
(42, 'HH0023', 'Rau muốn', 'Kg', NULL, 'LHH05', '0000-00-00', '0000-00-00', 'Việt Nam', '8934567890147', NULL),
(3, 'HH003', 'Mực', 'Kg', '250000.00', 'LHH04', '2025-11-10', '2025-12-10', 'Việt Nam', '8934567890125', ''),
(37, 'HH0034', 'Sầu riêng', 'Thùng', NULL, 'LHH08', '2026-01-31', '2026-02-07', 'Việt Nam', '8934567890141', NULL),
(4, 'HH004', 'Tôm', 'Kg', '300000.00', 'LHH04', '2025-11-03', '2025-11-10', 'Việt Nam', '8934567890126', ''),
(40, 'HH0043', 'Mì hảo hảo', 'Thùng', NULL, 'LHH18', '0000-00-00', '0000-00-00', 'Việt Nam', '8934567890123', NULL),
(27, 'HH005', 'Bia Saigon', 'Thùng', '180000.00', 'LHH09', '2026-02-04', '2026-11-20', 'Việt Nam', '8934567890130', '12 lon/thùng'),
(39, 'HH0056', 'Cá mồi', 'Thùng', NULL, 'LHH14', '2026-02-03', '2027-10-07', 'Việt Nam', '8934567890141', NULL),
(28, 'HH006', 'Nước ngọt Coca-Cola', 'Thùng', '150000.00', 'LHH10', '2025-10-15', '2026-10-15', 'Việt Nam', '8934567890131', '12 chai/thùng'),
(29, 'HH007', 'Sữa tươi Vinamilk', 'Thùng', '360000.00', 'LHH11', '2025-10-20', '2025-11-20', 'Việt Nam', '8934567890132', '12 lít/thùng'),
(30, 'HH008', 'Dầu gội Pantene', 'Thùng', '850000.00', 'LHH26', '2026-01-26', '2027-05-27', 'Việt Nam', '8934567890140', '10 chai/thùng'),
(41, 'HH0087', 'Dầu gọi clear', 'Thùng', NULL, 'LHH26', '2026-02-02', '2027-01-27', 'Việt Nam', '8934567890141', NULL),
(31, 'HH009', 'Tã Bobby', 'Thùng', '1800000.00', 'LHH27', '2025-08-21', '2026-08-01', 'Việt Nam', '8934567890141', '10 gói/thùng'),
(32, 'HH010', 'Chất tẩy rửa Sunlight', 'Thùng', '450000.00', 'LHH28', '2026-02-02', '2027-01-27', 'Việt Nam', '8934567890142', '10 chai/thùng'),
(5, 'HH011', 'Thịt gà ta', 'Kg', '120000.00', 'LHH01', '2026-02-05', '2026-11-30', 'Việt Nam', '8934567890127', ''),
(6, 'HH012', 'Thịt vịt', 'Kg', '150000.00', 'LHH01', '2025-11-02', '2025-11-08', 'Việt Nam', '8934567890128', ''),
(7, 'HH013', 'Thịt chim bồ câu', 'Kg', '180000.00', 'LHH01', '2025-11-19', '2026-09-05', 'Việt Nam', '8934567890129', ''),
(8, 'HH014', 'Trứng gà', 'Hộp', '75000.00', 'LHH03', '2025-10-28', '2026-08-05', 'Việt Nam', '8934567890133', '30 trứng/hộp'),
(9, 'HH015', 'Trứng vịt', 'Hộp', '90000.00', 'LHH03', '2025-11-06', '2026-08-21', 'Việt Nam', '8934567890134', '30 trứng/hộp'),
(10, 'HH016', 'Trứng cút', 'Hộp', '45000.00', 'LHH03', '2026-02-05', '2026-08-12', 'Việt Nam', '8934567890135', '30 trứng/hộp'),
(11, 'HH017', 'Cá hồi', 'Kg', '350000.00', 'LHH02', '2026-02-05', '2026-07-10', 'Na Uy', '8934567890136', ''),
(12, 'HH018', 'Cá basa', 'Kg', '120000.00', 'LHH02', '2026-02-05', '2026-07-28', 'Việt Nam', '8934567890137', ''),
(13, 'HH019', 'Cá thu', 'Kg', '200000.00', 'LHH02', '2026-02-05', '2026-10-02', 'Việt Nam', '8934567890138', ''),
(14, 'HH020', 'Tôm sú', 'Kg', '320000.00', 'LHH04', '2025-11-04', '2025-11-14', 'Việt Nam', '8934567890139', ''),
(15, 'HH021', 'Mực ống', 'Kg', '250000.00', 'LHH04', '2026-02-03', '2026-05-21', 'Việt Nam', '8934567890143', ''),
(16, 'HH022', 'Táo', 'Kg', '40000.00', 'LHH08', '2025-11-01', '2025-11-10', 'Mỹ', '8934567890144', ''),
(17, 'HH023', 'Chuối', 'Kg', '30000.00', 'LHH08', '2025-11-02', '2025-11-12', 'Việt Nam', '8934567890145', ''),
(18, 'HH024', 'Cam', 'Kg', '45000.00', 'LHH08', '2025-11-03', '2025-11-13', 'Úc', '8934567890146', ''),
(19, 'HH025', 'Rau xà lách', 'Kg', '20000.00', 'LHH05', '2026-03-02', '2026-03-04', 'Việt Nam', '8934567890147', ''),
(20, 'HH026', 'Rau cải', 'Kg', '18000.00', 'LHH05', '2026-02-03', '2026-06-25', 'Việt Nam', '8934567890148', ''),
(21, 'HH027', 'Khoai tây', 'Kg', '15000.00', 'LHH06', '2026-02-04', '2027-01-27', 'Việt Nam', '8934567890149', ''),
(22, 'HH028', 'Cà rốt', 'Kg', '20000.00', 'LHH06', '2025-11-02', '2025-11-16', 'Việt Nam', '8934567890150', ''),
(23, 'HH029', 'Nấm kim châm', 'Hộp', '25000.00', 'LHH07', '2026-02-03', '2026-02-06', 'Việt Nam', '8934567890151', '200g/hộp'),
(24, 'HH030', 'Nấm hương', 'Hộp', '30000.00', 'LHH07', '2025-11-02', '2025-11-12', 'Việt Nam', '8934567890152', '200g/hộp'),
(25, 'HH031', 'Hoa hồng', 'Thùng', '500000.00', 'LHH05', '2026-02-03', '2026-10-08', 'Việt Nam', '8934567890153', '10 bó/thùng'),
(26, 'HH032', 'Hoa cúc', 'Thùng', '400000.00', 'LHH05', '2025-11-02', '2025-11-08', 'Việt Nam', '8934567890154', '10 bó/thùng');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kehanghoa`
--

CREATE TABLE `kehanghoa` (
  `STT` int(11) NOT NULL,
  `MaKeHang` char(10) NOT NULL,
  `MaHangHoa` char(10) NOT NULL,
  `MaLoaiKho` char(10) NOT NULL,
  `ViTri` varchar(100) NOT NULL,
  `TongSucChua` int(11) NOT NULL,
  `DaChua` int(11) NOT NULL,
  `ConTrong` int(11) GENERATED ALWAYS AS (`TongSucChua` - `DaChua`) STORED,
  `TrangThai` varchar(50) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `MucToiThieuCanhBao` int(11) NOT NULL DEFAULT 0 COMMENT 'Ngưỡng tồn kho tối thiểu – dưới mức này sẽ cảnh báo',
  `MucGioiHanCanhBao` int(11) NOT NULL DEFAULT 0 COMMENT 'Ngưỡng giới hạn cao hơn để cảnh báo sớm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `kehanghoa`
--

INSERT INTO `kehanghoa` (`STT`, `MaKeHang`, `MaHangHoa`, `MaLoaiKho`, `ViTri`, `TongSucChua`, `DaChua`, `TrangThai`, `GhiChu`, `MucToiThieuCanhBao`, `MucGioiHanCanhBao`) VALUES
(1, 'TK001', 'HH025', 'LK01', 'A-1-A1', 400, 35, 'Trống', '', 10, 350),
(2, 'TK002', 'HH026', 'LK01', 'A-2-A1', 100, 1, 'Trống', '', 15, 85),
(3, 'TK003', 'HH027', 'LK01', 'A-3-A1', 100, 23, 'Trống', '', 23, 90),
(4, 'TK004', 'HH028', 'LK01', 'A-4-A1', 100, 0, 'Trống', '', 0, 0),
(5, 'TK005', 'HH029', 'LK01', 'A-5-A1', 100, 0, 'Trống', '', 0, 0),
(6, 'TK006', 'HH030', 'LK01', 'B-1-B1', 100, 0, 'Trống', '', 0, 0),
(7, 'TK007', 'HH031', 'LK01', 'B-2-B1', 100, 0, 'Trống', '', 0, 0),
(8, 'TK008', 'HH032', 'LK01', 'B-3-B1', 500, 0, 'Trống', '', 0, 0),
(9, 'TK009', 'HH005', 'LK01', 'B-4-B1', 100, 1, 'Trống', '', 30, 80),
(10, 'TK010', 'HH006', 'LK01', 'B-5-B1', 100, 1, 'Trống', '', 0, 0),
(11, 'TK011', 'HH007', 'LK02', 'C-1-C1', 100, 0, 'Trống', '', 0, 0),
(12, 'TK012', 'HH011', 'LK02', 'C-2-C1', 100, 17, 'Trống', '', 0, 0),
(13, 'TK013', 'HH012', 'LK02', 'C-3-C1', 100, 0, 'Trống', '', 0, 0),
(14, 'TK014', 'HH013', 'LK02', 'C-4-C1', 100, 9, 'Trống', '', 0, 0),
(15, 'TK015', 'HH014', 'LK02', 'C-5-C1', 100, 7, 'Trống', '', 0, 0),
(16, 'TK016', 'HH015', 'LK02', 'C-6-C1', 100, 12, 'Trống', '', 0, 0),
(17, 'TK017', 'HH016', 'LK02', 'C-7-C1', 100, 23, 'Trống', '', 0, 0),
(18, 'TK018', 'HH001', 'LK03', 'D-1-D1', 100, 12, 'Trống', '', 0, 0),
(19, 'TK019', 'HH002', 'LK03', 'D-2-D1', 100, 5, 'Trống', '', 0, 0),
(20, 'TK020', 'HH003', 'LK03', 'D-3-D1', 100, 0, 'Trống', '', 0, 0),
(21, 'TK021', 'HH004', 'LK03', 'D-4-D1', 100, 0, 'Trống', '', 0, 0),
(22, 'TK022', 'HH017', 'LK03', 'D-5-D1', 100, 3, 'Trống', '', 0, 0),
(23, 'TK023', 'HH018', 'LK03', 'D-6-D1', 100, 6, 'Trống', '', 0, 0),
(24, 'TK024', 'HH019', 'LK03', 'D-7-D1', 100, 7, 'Trống', '', 0, 0),
(25, 'TK025', 'HH020', 'LK03', 'D-8-D1', 100, 3, 'Trống', '', 0, 0),
(26, 'TK026', 'HH021', 'LK03', 'D-9-D1', 100, 0, 'Trống', '', 0, 0),
(27, 'TK027', 'HH008', 'LK04', 'F-F1', 100, 50, 'Trống', '', 0, 0),
(28, 'TK028', 'HH009', 'LK04', 'F-F2', 100, 0, 'Trống', '', 0, 0),
(29, 'TK029', 'HH010', 'LK04', 'F-F3', 340, 120, 'Trống', '', 30, 330),
(31, 'TK030', 'HH0056', 'LK02', 'F-F4', 100, 0, 'Trống', NULL, 5, 80),
(32, 'TK031', 'HH0043', 'LK01', 'F-F5', 150, 0, 'Trống', NULL, 30, 140),
(33, 'TK032', 'HH0087', 'LK01', 'F-F1-1', 140, 44, 'Trống', NULL, 30, 130),
(34, 'TK033', 'HH0023', 'LK02', 'A-2-A2', 200, 0, 'Trống', NULL, 30, 210);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaidanhmuc`
--

CREATE TABLE `loaidanhmuc` (
  `STT` int(11) NOT NULL,
  `MaLoaiDanhMuc` char(10) NOT NULL,
  `TenLoaiDanhMuc` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `loaidanhmuc`
--

INSERT INTO `loaidanhmuc` (`STT`, `MaLoaiDanhMuc`, `TenLoaiDanhMuc`, `MoTa`, `GhiChu`) VALUES
(1, 'LDM01', 'Thực phẩm tươi sống', 'Các loại thực phẩm tươi: thịt, cá, rau, củ, quả', ''),
(2, 'LDM02', 'Thực phẩm chế biến & đồ uống', 'Các loại thực phẩm đã chế biến và đồ uống', ''),
(3, 'LDM03', 'Sản phẩm chăm sóc cá nhân & gia đình', 'Sản phẩm chăm sóc cơ thể, vệ sinh, và gia dụng', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaihanghoa`
--

CREATE TABLE `loaihanghoa` (
  `STT` int(11) NOT NULL,
  `MaLoaiHang` char(10) NOT NULL,
  `MaDanhMuc` char(10) NOT NULL,
  `TenLoaiHang` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `loaihanghoa`
--

INSERT INTO `loaihanghoa` (`STT`, `MaLoaiHang`, `MaDanhMuc`, `TenLoaiHang`, `MoTa`) VALUES
(1, 'LHH01', 'DM01', 'Thịt', 'Các loại thịt tươi sống'),
(2, 'LHH02', 'DM01', 'Cá', 'Các loại cá tươi sống'),
(3, 'LHH03', 'DM01', 'Trứng', 'Các loại trứng'),
(4, 'LHH04', 'DM01', 'Hải sản', 'Các loại hải sản tươi sống'),
(5, 'LHH05', 'DM02', 'Rau', 'Các loại rau tươi'),
(6, 'LHH06', 'DM02', 'Củ', 'Các loại củ tươi'),
(7, 'LHH07', 'DM02', 'Nấm', 'Các loại nấm'),
(8, 'LHH08', 'DM02', 'Trái cây', 'Các loại trái cây tươi'),
(9, 'LHH09', 'DM03', 'Bia', 'Các loại bia'),
(10, 'LHH10', 'DM03', 'Nước giải khát', 'Nước ngọt, nước ép'),
(11, 'LHH11', 'DM04', 'Sữa', 'Sữa tươi, sữa tiệt trùng, sữa bột'),
(12, 'LHH12', 'DM05', 'Gạo', 'Các loại gạo'),
(13, 'LHH13', 'DM05', 'Bột', 'Bột mì, bột năng, bột ngô'),
(14, 'LHH14', 'DM05', 'Đồ khô', 'Các loại thực phẩm khô'),
(15, 'LHH15', 'DM06', 'Dầu ăn', 'Dầu thực vật, dầu oliu'),
(16, 'LHH16', 'DM06', 'Nước chấm', 'Nước mắm, tương ớt, xì dầu'),
(17, 'LHH17', 'DM06', 'Gia vị', 'Muối, tiêu, hạt nêm, bột ngọt'),
(18, 'LHH18', 'DM07', 'Mì', 'Mì tươi, mì khô'),
(19, 'LHH19', 'DM07', 'Miến', 'Các loại miến'),
(20, 'LHH20', 'DM07', 'Cháo', 'Cháo ăn liền'),
(21, 'LHH21', 'DM07', 'Phở', 'Phở ăn liền'),
(22, 'LHH22', 'DM08', 'Kem', 'Các loại kem'),
(23, 'LHH23', 'DM08', 'Sữa chua', 'Sữa chua ăn liền'),
(24, 'LHH24', 'DM09', 'Thực phẩm đông mát', 'Thịt, hải sản, rau củ đông mát'),
(25, 'LHH25', 'DM10', 'Bánh kẹo', 'Các loại bánh, kẹo'),
(26, 'LHH26', 'DM11', 'Chăm sóc cá nhân', 'Sữa tắm, dầu gội, kem đánh răng'),
(27, 'LHH27', 'DM12', 'Sản phẩm cho mẹ và bé', 'Tã, bỉm, sữa công thức'),
(28, 'LHH28', 'DM13', 'Đồ dùng gia đình', 'Chất tẩy rửa, đồ dùng nhà bếp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaikho`
--

CREATE TABLE `loaikho` (
  `STT` int(11) NOT NULL,
  `MaLoaiKho` char(10) NOT NULL,
  `TenLoaiKho` varchar(100) NOT NULL,
  `NhietDo` varchar(50) DEFAULT NULL,
  `TongSucChua` int(11) NOT NULL,
  `DaChua` int(11) DEFAULT NULL,
  `ConTrong` int(11) GENERATED ALWAYS AS (`TongSucChua` - `DaChua`) STORED,
  `MoTa` varchar(255) DEFAULT NULL,
  `TrangThai` varchar(50) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `loaikho`
--

INSERT INTO `loaikho` (`STT`, `MaLoaiKho`, `TenLoaiKho`, `NhietDo`, `TongSucChua`, `DaChua`, `MoTa`, `TrangThai`, `GhiChu`) VALUES
(1, 'LK01', 'Kho Thường', '+25°C đến +30°C', 5000, 500, 'Kho chứa hàng khô, hàng tiêu dùng. Bao gồm các khu A1-A5 và B1-B5.', 'Đang hoạt động', NULL),
(2, 'LK02', 'Kho Mát D1', '0°C đến +15°C', 2100, 300, 'Khu kho mát D1 dùng bảo quản hàng cần nhiệt độ thấp.', 'Đang hoạt động', NULL),
(3, 'LK03', 'Kho Đông D2', '-18°C đến -25°C', 1800, 200, 'Kho trữ đông thực phẩm đông lạnh D2.', 'Đang hoạt động', NULL),
(4, 'LK04', 'Kho Âm Sâu D3', '-30°C đến -60°C', 300, 50, 'Kho âm sâu dùng cho hàng đặc biệt, nhiệt độ siêu thấp.', 'Đang hoạt động', 'Cần kiểm tra định kỳ máy lạnh.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loainhacungcap`
--

CREATE TABLE `loainhacungcap` (
  `STT` int(11) NOT NULL,
  `MaLoaiNCC` char(10) NOT NULL,
  `TenLoaiNCC` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `loainhacungcap`
--

INSERT INTO `loainhacungcap` (`STT`, `MaLoaiNCC`, `TenLoaiNCC`, `MoTa`) VALUES
(1, 'LNC01', 'Thực phẩm tươi sống', 'Nhà cung cấp thịt, cá, rau củ quả tươi'),
(2, 'LNC02', 'Thực phẩm chế biến & đồ uống', 'Nhà cung cấp đồ uống, sữa, thực phẩm chế biến sẵn'),
(3, 'LNC03', 'Sản phẩm chăm sóc cá nhân & gia đình', 'Nhà cung cấp hóa phẩm, mỹ phẩm, đồ dùng gia đình');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loainhanvien`
--

CREATE TABLE `loainhanvien` (
  `STT` int(11) NOT NULL,
  `MaLoaiNV` char(10) NOT NULL,
  `TenLoaiNV` varchar(100) NOT NULL,
  `GhiChu` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `loainhanvien`
--

INSERT INTO `loainhanvien` (`STT`, `MaLoaiNV`, `TenLoaiNV`, `GhiChu`) VALUES
(1, 'LNV01', 'Nhân viên', 'Nhân viên làm việc lâu dài, hưởng đầy đủ quyền lợi'),
(2, 'LNV02', 'Nhân viên part-time', 'Nhân viên làm việc bán thời gian'),
(3, 'LNV03', 'Nhân viên full-time', 'Nhân viên làm việc toàn thời gian');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loainhaphanphoi`
--

CREATE TABLE `loainhaphanphoi` (
  `STT` int(11) NOT NULL,
  `MaLoaiNPP` char(10) NOT NULL,
  `TenLoaiNPP` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `loainhaphanphoi`
--

INSERT INTO `loainhaphanphoi` (`STT`, `MaLoaiNPP`, `TenLoaiNPP`, `MoTa`) VALUES
(1, 'LNPP01', 'Khu Vực Quận 1', 'Phân phối trực tiếp cho cửa hàng Bách Hóa Xanh Quận 1'),
(2, 'LNPP02', 'Khu Vực Quận 2', 'Phân phối trực tiếp cho cửa hàng Bách Hóa Xanh Quận 2'),
(3, 'LNPP03', 'Khu Vực Quận 3', 'Phân phối trực tiếp cho cửa hàng Bách Hóa Xanh Quận 3'),
(4, 'LNPP04', 'Khu Vực Quận 4', 'Phân phối trực tiếp cho cửa hàng Bách Hóa Xanh Quận 4'),
(5, 'LNPP05', 'Khu Vực Quận 5', 'Phân phối trực tiếp cho cửa hàng Bách Hóa Xanh Quận 5'),
(6, 'LNPP06', 'Khu Vực Quận 6', 'Phân phối trực tiếp cho cửa hàng Bách Hóa Xanh Quận 6'),
(7, 'LNPP07', 'Khu Vực Quận 7', 'Phân phối trực tiếp cho cửa hàng Bách Hóa Xanh Quận 7'),
(8, 'LNPP08', 'Khu Vực Quận 8', 'Phân phối trực tiếp cho cửa hàng Bách Hóa Xanh Quận 8');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `luanchuyenhanghoa`
--

CREATE TABLE `luanchuyenhanghoa` (
  `STT` int(11) NOT NULL,
  `MaLuanChuyen` char(10) NOT NULL,
  `MaHangHoa` char(10) NOT NULL,
  `NoiDi` varchar(255) NOT NULL,
  `NoiDen` varchar(255) NOT NULL,
  `ThoiGian` date NOT NULL,
  `TrangThai` varchar(50) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacungcap`
--

CREATE TABLE `nhacungcap` (
  `STT` int(11) NOT NULL,
  `MaNCC` char(10) NOT NULL,
  `TenNCC` varchar(100) NOT NULL,
  `ChuSoHuu` varchar(100) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `MaLoaiNCC` char(10) NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `nhacungcap`
--

INSERT INTO `nhacungcap` (`STT`, `MaNCC`, `TenNCC`, `ChuSoHuu`, `DiaChi`, `SoDienThoai`, `Email`, `MaLoaiNCC`, `GhiChu`) VALUES
(1, 'NCC001', 'Công ty Thịt An Phát', 'Nguyễn Văn A', '123 Lê Lợi, Q1, TP.HCM', '0901000001', 'anphat@gmail.com', 'LNC01', NULL),
(2, 'NCC002', 'Công ty Hải Sản Hoàng Gia', 'Trần Thị B', '456 Trần Hưng Đạo, Q2, TP.HCM', '0901000002', 'hoanggia@gmail.com', 'LNC01', NULL),
(3, 'NCC003', 'Công ty Rau Củ Hồng Phát', 'Lê Văn C', '789 Nguyễn Huệ, Q3, TP.HCM', '0901000003', 'hongphat@gmail.com', 'LNC01', NULL),
(4, 'NCC004', 'Công ty Trứng Minh Tâm', 'Phạm Thị D', '101 Lý Tự Trọng, Q4, TP.HCM', '0901000004', 'minhtam@gmail.com', 'LNC01', NULL),
(5, 'NCC005', 'Công ty Nấm Hoa Tươi', 'Vũ Minh H', '202 Pasteur, Q5, TP.HCM', '0901000005', 'hoatuoi@gmail.com', 'LNC01', NULL),
(6, 'NCC006', 'Công ty Bia Nước Ngọt Phát Lộc', 'Ngô Thị L', '303 Võ Văn Kiệt, Q6, TP.HCM', '0901000006', 'phatloc@gmail.com', 'LNC02', NULL),
(7, 'NCC007', 'Công ty Sữa Bách Khoa', 'Phan Văn S', '404 Nguyễn Tri Phương, Q7, TP.HCM', '0901000007', 'bachkhoa@gmail.com', 'LNC02', NULL),
(8, 'NCC008', 'Công ty Dầu Ăn Đại Phát', 'Nguyễn Thị T', '505 Hai Bà Trưng, Q8, TP.HCM', '0901000008', 'daiphat@gmail.com', 'LNC03', NULL),
(9, 'NCC009', 'Công ty Bánh Kẹo An Khang', 'Lê Thị M', '606 Cách Mạng Tháng 8, Q1, TP.HCM', '0901000009', 'ankhang@gmail.com', 'LNC03', NULL),
(10, 'NCC010', 'Công ty Đồ Gia Dụng Thịnh Vượng', 'Phạm Văn N', '707 Lê Lai, Q2, TP.HCM', '0901000010', 'thinhvuong@gmail.com', 'LNC02', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `STT` int(11) NOT NULL,
  `MaNV` char(10) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `GioiTinh` varchar(10) DEFAULT NULL,
  `DiaChi` varchar(200) DEFAULT NULL,
  `SDT` varchar(15) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `MaLoaiNV` char(10) NOT NULL,
  `ChucVu` varchar(50) DEFAULT NULL,
  `TrangThai` varchar(30) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `Avatar` varchar(255) DEFAULT 'user.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`STT`, `MaNV`, `HoTen`, `NgaySinh`, `GioiTinh`, `DiaChi`, `SDT`, `Email`, `MaLoaiNV`, `ChucVu`, `TrangThai`, `GhiChu`, `Avatar`) VALUES
(1, 'NV001', 'Nguyễn Văn An', '1980-03-04', 'Nam', '123 Đường A, Hà Nội', '0912345678', 'an.nguyen@example.com', 'LNV01', 'Giám đốc', 'Đang làm', 'Ca chiều, về sớm 20 phút, lý do đi thăm bệnh, anh duyệt nhé!\r\n', 'user.jpg'),
(2, 'NV002', 'Trần Thị Bích', '1985-07-12', 'Nữ', '456 Đường B, TP HCM', '0987654321', 'bich.tran@example.com', 'LNV01', 'Kế toán trưởng', 'Đang làm', 'Tăng ca ', 'user.jpg'),
(3, 'NV003', 'Lê Văn Cường', '1992-11-20', 'Nam', '789 Đường C, Đà Nẵng', '0911223344', 'cuong.le@example.com', 'LNV03', 'Nhân viên nhập kho', 'Đang làm', 'Ca sáng', 'user.jpg'),
(4, 'NV004', 'Phạm Thị Dung', '1988-05-15', 'Nữ', '321 Đường D, Hải Phòng', '0977665544', 'dung.pham@example.com', 'LNV03', 'Nhân viên xuất kho', 'Đang làm', 'Ca chiều', 'user.jpg'),
(5, 'NV005', 'Vũ Minh Hùng', '1993-09-09', 'Nam', '654 Đường E, Cần Thơ', '0909876543', 'hung.vu@example.com', 'LNV03', 'Thủ kho', 'Đang làm', 'Đi trễ 15p hôm 4-3 nha ', 'user.jpg'),
(6, 'NV006', 'Ngô Thị Lan', '1990-01-20', 'Nữ', '987 Đường F, Hà Nội', '0922334455', 'lan.ngo@example.com', 'LNV02', 'Bảo vệ', 'Đang làm', 'Ca đêm', 'user.jpg'),
(7, 'NV007', 'Phan Văn Sơn', '1995-06-10', 'Nam', '321 Đường G, TP HCM', '0933445566', 'son.phan@example.com', 'LNV02', 'Người vệ sinh', 'Đang làm', 'Ca sáng và chiều', 'user.jpg'),
(15, 'NV008', 'Lê Chí Trung', '2025-12-09', 'Nam', '', '0337718805', 'Trung@gmail.com', 'LNV01', '', 'Nghỉ việc', 'nào đi làm lại vậy em ', 'user.jpg'),
(16, 'NV009', 'Nguyễn Văn Trọng Trí ', '2004-03-04', 'Nam', 'HCM', '0337718805', 'trongtriww@gmail.com', 'LNV01', 'Giám đốc', 'Nghỉ làm ', 'Không bỏ cuộc ', 'user.jpg'),
(17, 'NV010', 'Khương Thị Trúc My ', '2024-12-05', 'Nữ', 'Bình Phước', '0324643245', 'my123@gmail.com', 'LNV01', 'Thủ kho', 'Trốn việc ', 'Làm cái chó gì ', 'user.jpg'),
(18, 'NV011', 'Dương Tấn Phát ', '2004-10-06', 'Nam', 'Quận 7 ', '012345695433', 'phat123@gmail.com', 'LNV01', 'thực tập ', 'Đang làm ', 'éo thích nữa', 'user.jpg'),
(19, 'NV012', 'Đoàn Thị Bảo Yến ', '2004-03-04', 'Nữ', '', '0424654725', 'yen123@gmail.com', 'LNV01', 'Nhân viên', 'Nghỉ làm ', 'éo thích làm ', 'user.jpg'),
(20, 'NV013', 'Lê Chí Phong ', '2004-09-02', 'Nữ', 'Vĩnh Long ', '0333333333336', 'phong123@gmail.com', 'LNV01', 'Nhân viên cố định', 'Nghỉ làm ', 'gần nhà a Jack 97 ', 'user.jpg'),
(21, 'NV014', '123', NULL, 'Nam', '', '02213454333', 'admin@gmail.com', 'LNV01', 'Nhân viên cố định', 'Ngừng làm việc', '', 'user.jpg'),
(22, 'NV015', 'yen', NULL, 'Nữ', '', '0359617482', 'dyen87088@gmail.com', 'LNV01', 'Nhân viên cố định', 'Ngừng làm việc', '', 'user.jpg'),
(23, 'NV016', 'Quản trị viên', '1969-12-31', 'Nam', '128, Phường Tân Đinh, thành phố Hồ Chí Minh', '0337718805', 'Quantrivien@gmail.com', 'LNV01', 'Quản trị viên', 'Đang làm', '', 'user.jpg'),
(24, 'NV017', 'Trần Thị Anh Thư', '2004-10-20', 'Nữ', '66 , Đường Trần Thị Nơi', '08745662239', 'anhThuTran@gmail.com', 'LNV01', 'Nhân viên', 'Đang làm', 'yêu thích công việc này', 'user.jpg'),
(26, 'NV018', 'me', '1969-12-31', 'Nữ', '', '0984237765', 'me123@gmail.com', 'LNV01', 'Giám đốc', 'Đang làm', 'cfrbyukawje', 'user.jpg'),
(27, 'NV019', 'Người dùng', '0000-00-00', 'Nam', '', '033771880503377', 'Nguoidung@gmail.com', 'LNV01', 'Giám đốc', 'Ngừng làm việc', '', 'user.jpg'),
(28, 'NV020', 'nguyenvanbao', '0000-00-00', 'Nam', '', '0337718805', 'trongtriww@gmail.com', 'LNV01', 'Nhân viên', 'Ngừng làm việc', '', 'user.jpg'),
(29, 'NV021', 'Nguyễn Văn An', '2004-01-30', 'Nam', '128, Phường Tân Đinh, thành phố Hồ Chí Minh', '0432455643', 'nguyenvana@gamil.com', 'LNV01', 'Nhân viên', 'Ngừng làm việc', 'Anh không duyệt nhé\r\n', 'user.jpg'),
(30, 'NV022', 'aaaaa', NULL, 'Nữ', '', '0432455643', 'nguyenvana@gamil.com', 'LNV01', 'Nhân viên', 'Ngừng làm việc', '', 'user.jpg'),
(31, 'NV023', 'aaaaa', NULL, '', '', '0432455643', 'nguyenvana@gamil.com', 'LNV01', 'Nhân viên', 'Đang làm', '', 'user.jpg'),
(32, 'NV024', 'Admin', '0000-00-00', 'Nam', '', '0432455643', 'nguyenvana@gamil.com', 'LNV01', 'Giám đốc', 'Đang làm', '', 'user.jpg'),
(33, 'NV025', 'Trần Thanh Quốc ', NULL, 'Nam', '', '0324643245', 'thanhquoc@gmail.com', 'LNV01', 'Nhân viên', 'Đang làm', '', 'user.jpg'),
(34, 'NV026', 'Nguyễn Minh Mẫn ', NULL, 'Nam', '', '0324643245', 'thanhquoc@gmail.com', 'LNV01', 'Nhân viên', 'Đang làm', '', 'user.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhaphanphoi`
--

CREATE TABLE `nhaphanphoi` (
  `STT` int(11) NOT NULL,
  `MaNPP` char(10) NOT NULL,
  `TenNPP` varchar(100) NOT NULL,
  `ChuSoHuu` varchar(100) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `MaLoaiNPP` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `nhaphanphoi`
--

INSERT INTO `nhaphanphoi` (`STT`, `MaNPP`, `TenNPP`, `ChuSoHuu`, `DiaChi`, `SoDienThoai`, `Email`, `MaLoaiNPP`) VALUES
(1, 'NPP001', 'BHX Q1 - 01', 'Nguyễn Văn A', '123 Lê Lợi, Q1, TP.HCM', '0901000001', 'q101@bhx.com', 'LNPP01'),
(2, 'NPP002', 'BHX Q1 - 02', 'Trần Thị B', '150 Nguyễn Huệ, Q1, TP.HCM', '0901000002', 'q102@bhx.com', 'LNPP01'),
(3, 'NPP003', 'BHX Q1 - 03', 'Lê Văn C', '200 Đồng Khởi, Q1, TP.HCM', '0901000003', 'q103@bhx.com', 'LNPP01'),
(4, 'NPP004', 'BHX Q2 - 01', 'Phạm Thị D', '12 Xa Lộ Hà Nội, Q2, TP.HCM', '0902000001', 'q201@bhx.com', 'LNPP02'),
(5, 'NPP005', 'BHX Q2 - 02', 'Nguyễn Văn E', '34 Thảo Điền, Q2, TP.HCM', '0902000002', 'q202@bhx.com', 'LNPP02'),
(6, 'NPP006', 'BHX Q2 - 03', 'Trần Văn F', '56 An Phú, Q2, TP.HCM', '0902000003', 'q203@bhx.com', 'LNPP02'),
(7, 'NPP007', 'BHX Q3 - 01', 'Lê Thị G', '78 Hai Bà Trưng, Q3, TP.HCM', '0903000001', 'q301@bhx.com', 'LNPP03'),
(8, 'NPP008', 'BHX Q3 - 02', 'Phạm Văn H', '90 Võ Thị Sáu, Q3, TP.HCM', '0903000002', 'q302@bhx.com', 'LNPP03'),
(9, 'NPP009', 'BHX Q4 - 01', 'Nguyễn Thị I', '12 Tôn Thất Thuyết, Q4, TP.HCM', '0904000001', 'q401@bhx.com', 'LNPP04'),
(10, 'NPP010', 'BHX Q4 - 02', 'Lê Văn J', '45 Bến Vân Đồn, Q4, TP.HCM', '0904000002', 'q402@bhx.com', 'LNPP04'),
(11, 'NPP011', 'BHX Q5 - 01', 'Trần Thị K', '23 Trần Hưng Đạo, Q5, TP.HCM', '0905000001', 'q501@bhx.com', 'LNPP05'),
(12, 'NPP012', 'BHX Q5 - 02', 'Phạm Văn L', '56 Nguyễn Trãi, Q5, TP.HCM', '0905000002', 'q502@bhx.com', 'LNPP05'),
(13, 'NPP013', 'BHX Q6 - 01', 'Nguyễn Văn M', '78 Hậu Giang, Q6, TP.HCM', '0906000001', 'q601@bhx.com', 'LNPP06'),
(14, 'NPP014', 'BHX Q6 - 02', 'Lê Thị N', '90 Lý Thường Kiệt, Q6, TP.HCM', '0906000002', 'q602@bhx.com', 'LNPP06'),
(15, 'NPP015', 'BHX Q7 - 01', 'Trần Văn O', '12 Nguyễn Thị Thập, Q7, TP.HCM', '0907000001', 'q701@bhx.com', 'LNPP07'),
(16, 'NPP016', 'BHX Q7 - 02', 'Phạm Thị P', '34 Lê Văn Lương, Q7, TP.HCM', '0907000002', 'q702@bhx.com', 'LNPP07'),
(17, 'NPP017', 'BHX Q8 - 01', 'Nguyễn Văn Q', '56 Phạm Thế Hiển, Q8, TP.HCM', '0908000001', 'q801@bhx.com', 'LNPP08'),
(18, 'NPP018', 'BHX Q8 - 02', 'Lê Thị R', '78 Tạ Quang Bửu, Q8, TP.HCM', '0908000002', 'q802@bhx.com', 'LNPP08'),
(19, 'NPP019', 'BHX Q1 - 04', 'Nguyễn Văn S', '210 Lê Lợi, Q1, TP.HCM', '0901000004', 'q104@bhx.com', 'LNPP01'),
(20, 'NPP020', 'BHX Q1 - 05', 'Trần Thị T', '220 Nguyễn Huệ, Q1, TP.HCM', '0901000005', 'q105@bhx.com', 'LNPP01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhap`
--

CREATE TABLE `phieunhap` (
  `STT` int(11) NOT NULL,
  `MaPhieuNhap` char(10) NOT NULL,
  `ThoiGian` datetime DEFAULT NULL,
  `MaNhanVien` char(10) NOT NULL,
  `MaNCC` char(10) NOT NULL,
  `DonVi` varchar(100) DEFAULT NULL,
  `BoPhan` varchar(100) DEFAULT NULL,
  `DiaDiem` varchar(255) DEFAULT NULL,
  `MaDonViVanChuyen` char(10) DEFAULT NULL,
  `NhapTaiKho` varchar(100) DEFAULT NULL,
  `DiaChi` varchar(100) DEFAULT NULL,
  `NguoiGiaoHang` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `phieunhap`
--

INSERT INTO `phieunhap` (`STT`, `MaPhieuNhap`, `ThoiGian`, `MaNhanVien`, `MaNCC`, `DonVi`, `BoPhan`, `DiaDiem`, `MaDonViVanChuyen`, `NhapTaiKho`, `DiaChi`, `NguoiGiaoHang`) VALUES
(150, 'pn0012', '2026-01-08 18:08:00', 'NV021', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường Chợ Mới', 'DV001', '', 'Thủ đức', 'Nguyễn Văn Bảo'),
(158, 'PN00343', '2026-01-31 10:34:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', '', 'Thủ đưc', 'Trần Tiến Đạt'),
(159, 'PN00344', '2026-01-31 11:14:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(160, 'PN00345', '2026-01-31 11:17:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(161, 'PN00346', '2026-01-31 11:27:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(162, 'PN00347', '2026-01-31 11:27:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(163, 'PN00348', '2026-01-31 14:58:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(164, 'PN00349', '2026-01-31 15:03:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(165, 'PN00350', '2026-01-31 15:04:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(166, 'PN00351', '2026-01-31 15:43:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(167, 'PN00352', '2026-01-31 15:44:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(169, 'PN00355', '2026-02-02 12:22:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(170, 'PN00356', '2026-02-02 12:27:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(173, 'PN00357', '2026-02-02 13:26:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(171, 'PN00358', '2026-02-02 12:28:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(172, 'PN00359', '2026-02-02 12:29:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đưc', 'Trần Tiến Minh '),
(207, 'PN004', '2026-03-03 08:21:00', 'NV016', 'NCC001', 'Phòng tài chính', 'Nhập kho', 'Phường Tân Định', 'DV001', 'LK02', '789 Nguyễn Huệ, Q3, TP.HCM', 'Nguyễn Văn A'),
(208, 'PN0041', '2026-03-03 08:26:00', 'NV016', 'NCC001', 'Phòng tài chính', 'Nhập kho', 'Phường Tân Định', 'DV001', 'LK02', '303 Võ Văn Kiệt, Q6, TP.HCM', 'Nguyễn Văn A'),
(209, 'PN0042', '2026-03-03 08:27:00', 'NV016', 'NCC001', 'Phòng tài chính', 'Nhập kho', 'Phường Tân Định', 'DV001', 'LK02', '101 Lý Tự Trọng, Q4, TP.HCM', 'Nguyễn Văn A'),
(157, 'PN00432', '2026-01-31 10:19:00', 'NV016', 'NCC001', 'Phòng kế toán ', 'Nhập kho ', 'Đường Mai Chí Thọ', 'DV001', 'A12', '128, Phường Chơ Lớn ', 'Trần Tiến Minh '),
(174, 'PN04035', '2026-02-02 16:05:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(175, 'PN04037', '2026-02-02 16:15:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(179, 'PN0403734', '2026-02-03 10:47:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(180, 'PN04037390', '2026-02-03 11:05:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(178, 'PN040376', '2026-02-03 10:45:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(202, 'PN04037621', '2026-02-04 10:42:00', 'NV009', 'NCC001', 'Phòng tài chính', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'LK03', '404 Nguyễn Tri Phương, Q7, TP.HCM', 'Nguyễn Văn B'),
(176, 'PN040378', '2026-02-03 10:13:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(181, 'PN0431', '2026-02-03 11:07:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(182, 'PN0432', '2026-02-03 11:09:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(195, 'PN043212', '2026-02-03 13:27:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(196, 'PN0432121', '2026-02-03 14:01:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(197, 'PN04321212', '2026-02-03 14:01:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(199, 'PN043232', '2026-02-03 14:05:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(200, 'PN04323212', '2026-02-03 14:06:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(183, 'PN0434', '2026-02-03 11:10:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(184, 'PN0436', '2026-02-03 11:33:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(185, 'PN0438', '2026-02-03 11:33:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(186, 'PN0439', '2026-02-03 11:39:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(187, 'PN04390', '2026-02-03 11:40:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(192, 'PN0439532', '2026-02-03 12:58:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(193, 'PN04395322', '2026-02-03 12:59:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(188, 'PN043954', '2026-02-03 11:44:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(189, 'PN043955', '2026-02-03 11:44:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(191, 'PN043956', '2026-02-03 12:57:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(190, 'PN043958', '2026-02-03 12:35:00', 'NV024', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Nguyễn Văn B'),
(168, 'PN0567', '2026-02-01 18:32:00', 'NV016', 'NCC001', 'Phòng kế toán ', 'Nhập kho', 'A12', 'DV001', 'C23', 'Ấp bình lợi', 'Đào Văn Tài '),
(151, 'PN3101', '2026-01-31 09:44:00', 'NV016', 'NCC001', 'Phòng kế toán ', 'Nhập kho ', 'Khu 2c,Phường Bến Lức , Tỉnh Tây Ninh', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Lê Anh Quân'),
(153, 'PN3201', '2026-01-31 09:47:00', 'NV016', 'NCC001', 'Phòng kế toán ', 'Nhập kho ', 'Khu 2c,Phường Bến Lức , Tỉnh Tây Ninh', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Lê Anh Quân'),
(154, 'PN3202', '2026-01-31 09:58:00', 'NV016', 'NCC001', 'Phòng kế toán ', 'Nhập kho ', 'Phường chánh hưng', 'DV001', 'A12', '266, đường Âu Dương Lân, Phường Chánh Hưng, TP HCM', 'Lê Anh Quân'),
(201, 'PN435', '2026-02-04 09:52:00', 'NV024', 'NCC001', 'Phòng tài chính', 'Nhập kho', 'Đường Mai Chí Thọ', 'DV001', 'LK01', 'q12', 'Trần Tiến Minh '),
(203, 'PN4522', '2026-02-05 08:33:00', 'NV009', 'NCC001', 'Phòng tài chính', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'LK01', '606 Cách Mạng Tháng 8, Q1, TP.HCM', 'Hồ Thành Khải'),
(204, 'PN4523', '2026-02-05 08:35:00', 'NV009', 'NCC001', 'Phòng tài chính', 'Nhập kho', 'Phường Chợ Mới', 'DV001', 'LK02', '606 Cách Mạng Tháng 8, Q1, TP.HCM', 'Phan Trọng Hiếu'),
(206, 'PN45232', '2026-02-05 08:57:00', 'NV009', 'NCC001', 'Phòng tài chính', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'LK02', '101 Lý Tự Trọng, Q4, TP.HCM', 'Trần Tiến Minh '),
(205, 'PN4525', '2026-02-05 08:38:00', 'NV009', 'NCC001', 'Phòng tài chính', 'Nhập kho', 'Phường Chợ Mới', 'DV001', 'LK03', '456 Trần Hưng Đạo, Q2, TP.HCM', 'Bùi Lê Nhật Nam '),
(148, 'PX0001', '2026-01-08 17:23:00', 'NV020', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường Chợ Mới', 'DV001', 'A12', 'Thủ đưc', 'Nguyễn Văn B'),
(149, 'PX0002', '2026-01-08 17:24:00', 'NV020', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường Chợ Mới', 'DV001', 'A12', 'Thủ đưc', 'Nguyễn Văn B'),
(139, 'PX002', '2025-12-29 18:18:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đức', 'Nguyễn Văn B'),
(140, 'PX003', '2025-12-29 18:26:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đức', 'Nguyễn Văn B'),
(141, 'PX004', '2025-12-29 20:00:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đức', 'Nguyễn Văn B'),
(142, 'PX005', '2025-12-30 18:29:00', 'NV009', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'DV001', 'A12', 'Thủ đức', 'Nguyễn Văn B'),
(146, 'PX008', '2026-01-08 17:13:00', 'NV020', 'NCC001', 'Bách hóa xanh', 'Nhập kho', 'Phường Chợ Mới', 'DV001', 'A12', 'Thủ đưc', 'Nguyễn Văn B');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieuxuat`
--

CREATE TABLE `phieuxuat` (
  `STT` int(11) NOT NULL,
  `MaPhieuXuat` char(10) NOT NULL,
  `ThoiGian` date NOT NULL,
  `MaNhanVien` char(10) NOT NULL,
  `MaNPP` char(10) NOT NULL,
  `DonVi` varchar(100) DEFAULT NULL,
  `BoPhan` varchar(100) NOT NULL,
  `DiaDiem` varchar(255) NOT NULL,
  `LyDo` varchar(255) DEFAULT NULL,
  `XuatTaiKho` varchar(255) DEFAULT NULL,
  `MaDonViVanChuyen` char(10) NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `NguoiNhanHang` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `phieuxuat`
--

INSERT INTO `phieuxuat` (`STT`, `MaPhieuXuat`, `ThoiGian`, `MaNhanVien`, `MaNPP`, `DonVi`, `BoPhan`, `DiaDiem`, `LyDo`, `XuatTaiKho`, `MaDonViVanChuyen`, `GhiChu`, `DiaChi`, `NguoiNhanHang`) VALUES
(1, 'PX001', '2025-11-06', 'NV001', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng', 'Kho A1', 'DV001', 'Xuất thịt heo từ An Phát', 'Phạm hùng ', 'Nguyễn Thị Thu phương'),
(23, 'PX0010', '2026-01-02', 'NV009', 'NPP001', 'Phòng kế toán ', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho C5', 'DV001', NULL, 'Thủ đưc', 'Nguyễn Thị Thu phương'),
(22, 'PX0011', '2025-12-30', 'NV009', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Đường Mai Chí Thọ', 'Thiếu hàng ', 'Kho C5', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(48, 'PX001124', '2026-02-04', 'NV009', 'NPP001', 'Phòng tài chính', 'Xuất kho', 'Đường Mai Chí Thọ', 'Thiếu hàng ', '', 'DV001', NULL, '12 Tôn Thất Thuyết, Q4, TP.HCM', 'Nguyễn Thị Thu phương'),
(24, 'px0012', '2026-01-08', 'NV020', 'NPP001', 'Phòng kế toán ', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho C5', 'DV001', NULL, 'Thủ đưc', 'Nguyễn Thị Thu phương'),
(16, 'px0013', '2025-12-25', 'NV009', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đưc', 'Nguyễn Thị Thu phương'),
(25, 'PX0014', '2026-01-08', 'NV020', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(26, 'PX0015', '2026-01-08', 'NV020', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(2, 'PX002', '2025-11-06', 'NV002', 'NPP002', 'Bách hóa xanh', 'Xuất kho', 'Phường Chợ Mới', 'Thiếu hàng', 'Kho A4', 'DV002', 'Xuất cá hồi từ Hoàng Gia', 'Âu dương lân', 'Lê Phan Minh Thư'),
(21, 'PX0020', '2025-12-30', 'NV009', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Đường Mai Chí Thọ', 'Thiếu hàng ', 'Kho C5', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(47, 'PX00212', '2026-02-04', 'NV009', 'NPP001', 'Phòng tài chính', 'Xuất kho', 'A12', 'Thiếu hàng ', '', 'DV001', NULL, '12 Tôn Thất Thuyết, Q4, TP.HCM', 'Đào Cát Tường '),
(28, 'px0023', '2026-01-08', 'NV016', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(43, 'PX00234', '2026-02-04', 'NV024', 'NPP001', 'Phòng tài chính', 'Xuất kho', 'A12', 'Thiếu hàng ', '', 'DV001', NULL, '12 Nguyễn Thị Thập, Q7, TP.HCM', 'Tô Thị Ánh Quyệt '),
(44, 'PX0023412', '2026-02-04', 'NV024', 'NPP001', 'Phòng tài chính', 'Xuất kho', 'A12', 'Thiếu hàng ', '', 'DV001', NULL, '123 Lê Lợi, Q1, TP.HCM', 'Đào Cát Tường '),
(29, 'px0024', '2026-01-31', 'NV016', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(31, 'px0025', '2026-02-02', 'NV024', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(33, 'px0026', '2026-02-02', 'NV024', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(34, 'px0027', '2026-02-02', 'NV024', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(35, 'px0028', '2026-02-02', 'NV009', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(3, 'PX003', '2025-11-06', 'NV003', 'NPP003', 'Bách hóa xanh', 'Xuất kho', 'Chợ Bình Điền', 'Bổ sung hàng', 'Kho B1', 'DV003', 'Xuất rau củ quả từ Hồng Phát', 'Chi Nhánh Gò vấp', 'Dương Minh Tuấn'),
(4, 'PX004', '2025-11-06', 'NV004', 'NPP004', 'Bách hóa xanh', 'Xuất kho', 'Cầu Nguyễn Văn Cừ', 'Bổ sung hàng', 'Kho A16', 'DV004', 'Xuất trứng gà từ Minh Tâm', 'Chi Nhánh An Xương', 'Phan Tuấn Anh'),
(5, 'PX005', '2025-11-06', 'NV005', 'NPP005', 'Bách hóa xanh', 'Xuất kho', 'Đường Mạc Đỉnh Chi', 'Bổ sung hàng', 'Kho C3', 'DV005', 'Xuất nấm và hoa tươi từ Thành Công', 'Chi Nhánh Tân bình', 'Lê Thi Yến Ngọc'),
(27, 'PX0056', '2026-01-08', 'NV020', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(17, 'PX008', '2025-12-30', 'NV009', 'NPP001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho C5', 'DV001', NULL, 'Thủ đưc', 'Nguyễn Thị Thu phương'),
(37, 'PX00880', '2026-02-10', 'NV024', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đưc', 'Nguyễn Thị Thu phương'),
(36, 'PX00886', '2026-02-02', 'NV024', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đưc', 'Nguyễn Thị Thu phương'),
(38, 'PX00898', '2026-02-03', 'NV009', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho A14', 'DV001', NULL, 'Thủ đưc', 'Nguyễn Thị Thu phương'),
(19, 'PX009', '2025-12-29', 'NV009', 'NPP001', 'Bách hóa xanh', 'Nhập kho', 'Phường chánh hưng', 'Thiếu hàng ', 'Kho C5', 'DV001', NULL, 'Thủ đưc', 'Nguyễn Thị Thu phương'),
(20, 'PX010', '2025-12-30', 'NV009', 'NPP001', 'Bách hóa xanh', 'Xuất kho', 'Đường Mai Chí Thọ', 'Thiếu hàng ', 'Kho C5', 'DV001', NULL, 'Thủ đức', 'Nguyễn Thị Thu phương'),
(30, 'PX0657', '2026-02-01', 'NV016', 'NPP001', 'Phòng kế toán ', 'Xuất kho', 'A12', 'Bổ sung hàng', 'C23', 'DV001', NULL, 'Ấp bình lợi', 'Lân '),
(46, 'PX323', '2026-02-04', 'NV024', 'NPP001', 'Phòng tài chính', 'Xuất kho', 'A12', 'Thiếu hàng ', 'LK03', 'DV001', NULL, '12 Tôn Thất Thuyết, Q4, TP.HCM', 'Đào Cát Tường ');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `STT` int(11) DEFAULT NULL,
  `MaTK` varchar(10) NOT NULL,
  `MaNV` char(10) DEFAULT NULL,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `GioiTinh` varchar(10) DEFAULT NULL,
  `SDT` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `DiaChi` varchar(200) DEFAULT NULL,
  `TrangThai` varchar(10) DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  `trangthaihoatdong` datetime DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `Avatar` varchar(255) DEFAULT 'default.png',
  `danghoatdong` tinyint(1) DEFAULT 0,
  `isOnline` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`STT`, `MaTK`, `MaNV`, `TenDangNhap`, `MatKhau`, `GioiTinh`, `SDT`, `Email`, `DiaChi`, `TrangThai`, `NgayTao`, `trangthaihoatdong`, `GhiChu`, `Avatar`, `danghoatdong`, `isOnline`) VALUES
(1, 'TK001', 'NV008', 'Lê Chí Trung', 'Lê Chí Trung', 'Nam', '0337718805', 'Trung@gmail.com', NULL, NULL, '2025-12-11 13:56:30', NULL, NULL, 'default.png', 0, 0),
(2, 'TK002', 'NV009', 'Nguyễn Văn Trọng Trí ', '123', 'Nam', '0337718806', 'trongtriww@gmail.com', NULL, NULL, '2025-12-11 14:23:00', '2026-03-15 18:37:21', NULL, 'avatar_TK002.jpg', 0, 0),
(3, 'TK003', 'NV010', 'Khương Thị Trúc My ', '12012004', 'Nữ', '23743937949395', 'my123@gmail.com', NULL, NULL, '2025-12-11 17:35:18', '2026-03-05 09:12:42', NULL, 'avatar_TK003.jpg', 0, 0),
(4, 'TK004', 'NV011', 'Trương Tấn Phát ', 'Phat123', 'Nam', '012345695433', 'phat123@gmail.com', NULL, NULL, '2025-12-12 12:30:16', NULL, NULL, 'default.png', 0, 0),
(5, 'TK005', 'NV012', 'Đoàn Thị Bảo Yến ', 'Yen123', 'Không', '0424654725', 'yen123@gmail.com', NULL, NULL, '2025-12-12 14:39:24', '2026-03-15 17:52:57', NULL, 'default.png', 0, 0),
(6, 'TK006', 'NV013', 'Lê Chí Phong ', 'Phong123', 'Nữ', '0333333333336', 'phong123@gmail.com', NULL, NULL, '2025-12-20 11:43:22', NULL, NULL, 'default.png', 0, 0),
(NULL, 'TK007', 'NV016', 'Quản trị viên', 'Quantrivien123', 'Nam', '0337718805', 'Quantrivien@gmail.com', NULL, NULL, '2026-01-02 00:41:26', '2026-03-15 17:56:30', NULL, 'avatar_TK007.jpg', 0, 0),
(NULL, 'TK008', 'NV017', 'Trần Thị Anh Thư', 'AnhThu180304', 'Nữ', '08745662239', 'anhThuTran@gmail.com', NULL, NULL, '2026-01-02 01:34:58', NULL, NULL, 'default.png', 0, 0),
(NULL, 'TK009', 'NV018', 'me', '123456', 'Nữ', '0984237765', 'me123@gmail.com', NULL, NULL, '2026-01-04 21:22:30', NULL, NULL, 'avatar_TK009.jpg', 0, 0),
(NULL, 'TK010', 'NV019', 'Người dùng', 'nguoidung', 'Nam', '033771880503377', 'Nguoidung@gmail.com', NULL, NULL, '2026-01-05 00:56:41', NULL, NULL, 'default.png', 0, 0),
(NULL, 'TK011', 'NV020', 'nguyenvanbao', 'bao123', 'Nam', '0337718805', 'trongtriww@gmail.com', NULL, NULL, '2026-01-08 02:12:26', NULL, NULL, 'default.png', 0, 0),
(NULL, 'TK012', 'NV021', 'Nguyen Van A', '123', 'Nam', '0432455643', 'nguyenvana@gamil.com', NULL, NULL, '2026-01-08 03:04:05', NULL, NULL, 'default.png', 0, 0),
(NULL, 'TK013', 'NV024', 'Admin', '123', 'Nam', '0432455643', 'nguyenvana@gamil.com', NULL, NULL, '2026-01-30 18:17:29', NULL, NULL, 'default.png', 0, 0),
(NULL, 'TK014', 'NV025', 'Trần Thanh Quốc ', '123', 'Nam', '0324643245', 'thanhquoc@gmail.com', NULL, NULL, '2026-02-03 22:20:38', NULL, NULL, 'default.png', 0, 0),
(NULL, 'TK015', 'NV026', 'Nguyễn Minh Mẫn ', '123', 'Nam', '0324643245', 'thanhquoc@gmail.com', NULL, NULL, '2026-02-03 22:27:25', '2026-03-05 09:12:22', NULL, 'default.png', 0, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanhly`
--

CREATE TABLE `thanhly` (
  `STT` int(11) NOT NULL,
  `MaThanhLy` char(10) NOT NULL,
  `MaTonKho` char(10) NOT NULL,
  `MaHangHoa` char(10) NOT NULL,
  `ThoiGianThanhLy` date NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `LyDo` varchar(255) NOT NULL,
  `TrangThai` varchar(50) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tinnhan`
--

CREATE TABLE `tinnhan` (
  `MaTinNhan` int(11) NOT NULL,
  `MaCHT` int(11) NOT NULL,
  `MaTK_Gui` varchar(10) NOT NULL,
  `NoiDung` text NOT NULL,
  `ThoiGian` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `tinnhan`
--

INSERT INTO `tinnhan` (`MaTinNhan`, `MaCHT`, `MaTK_Gui`, `NoiDung`, `ThoiGian`) VALUES
(434, 17, 'TK007', 'chức năng mới đã được cập nhật', '2026-03-14 09:58:11'),
(435, 18, 'TK002', 'okk em', '2026-03-15 18:06:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tinnhan_daxem`
--

CREATE TABLE `tinnhan_daxem` (
  `MaTinNhan` int(11) NOT NULL,
  `MaTK` varchar(10) NOT NULL,
  `ThoiGianXem` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `tinnhan_daxem`
--

INSERT INTO `tinnhan_daxem` (`MaTinNhan`, `MaTK`, `ThoiGianXem`) VALUES
(434, 'TK002', '2026-03-15 18:05:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tonkho`
--

CREATE TABLE `tonkho` (
  `STT` int(11) NOT NULL,
  `MaTonKho` char(10) NOT NULL,
  `MaDanhMuc` char(10) NOT NULL,
  `MaHangHoa` char(10) NOT NULL,
  `MaLoaiKho` char(10) NOT NULL,
  `TrangThai` varchar(50) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `tonkho`
--

INSERT INTO `tonkho` (`STT`, `MaTonKho`, `MaDanhMuc`, `MaHangHoa`, `MaLoaiKho`, `TrangThai`, `GhiChu`) VALUES
(72, 'TK001', 'DM11', 'HH008', 'LK04', 'Hết hàng', 'Nhập kho từ phiếu PX003'),
(73, 'TK002', 'DM12', 'HH009', 'LK04', '0', 'Nhập kho từ phiếu PX002'),
(74, 'TK003', 'DM02', 'HH012', 'LK02', '0', 'Nhập kho từ phiếu PX005'),
(77, 'TK004', 'DM03', 'HH032', 'LK01', '0', 'Nhập kho từ phiếu PC005'),
(78, 'TK005', 'DM01', 'HH001', 'LK03', 'Còn hàng', 'Nhập kho từ phiếu PC005'),
(79, 'TK006', 'DM01', 'HH003', 'LK03', '0', 'Nhập kho từ phiếu PX008'),
(80, 'TK007', 'DM03', 'HH005', 'LK01', 'Còn hàng', 'Nhập kho từ phiếu PX008'),
(83, 'TK009', 'DM04', 'hh007', 'LK02', '0', 'Nhập kho từ phiếu px0088'),
(84, 'TK010', 'DM13', 'hh010', 'LK04', '0', 'Nhập kho từ phiếu px3121'),
(85, 'TK011', 'DM02', 'hh011', 'LK02', '0', 'Nhập kho từ phiếu px3121'),
(86, 'TK012', 'DM01', 'hh004', 'LK03', '0', 'Nhập kho từ phiếu px006'),
(87, 'TK013', 'DM02', 'HH025', 'LK01', 'Còn hàng', 'Nhập kho từ phiếu PX009'),
(88, 'TK014', 'DM01', 'hh002', 'LK03', '0', 'Nhập kho từ phiếu pn644'),
(89, 'TK015', 'DM03', 'HH006', 'LK01', 'Hết hàng', 'Nhập kho từ phiếu px009'),
(90, 'TK016', 'DM09', 'HH0056', 'LK02', '0', 'Nhập kho từ phiếu PN00432'),
(91, 'TK017', 'DM02', 'HH031', 'LK01', '1', 'Nhập kho từ phiếu PN040376'),
(92, 'TK018', 'DM02', 'HH030', 'LK01', 'Hết hàng', 'Nhập kho từ phiếu PN0403734'),
(93, 'TK019', 'DM01', 'HH021', 'LK03', '1', 'Nhập kho từ phiếu PN043212'),
(94, 'TK020', 'DM02', 'HH027', 'LK01', '1', 'Nhập kho từ phiếu PN0432121'),
(95, 'TK021', 'DM02', 'HH029', 'LK01', '1', 'Nhập kho từ phiếu PN043232'),
(96, 'TK022', 'DM02', 'HH026', 'LK01', 'Còn hàng', 'Nhập kho từ phiếu PN04323212'),
(97, 'TK023', 'DM01', 'HH019', 'LK03', 'Hết hàng', 'Nhập kho từ phiếu PN435'),
(98, 'TK024', 'DM01', 'HH018', 'LK03', '1', 'Nhập kho từ phiếu PN4522'),
(99, 'TK025', 'DM01', 'HH017', 'LK03', '1', 'Nhập kho từ phiếu PN4522'),
(100, 'TK026', 'DM01', 'HH015', 'LK02', '1', 'Nhập kho từ phiếu PN4523'),
(101, 'TK027', 'DM01', 'HH014', 'LK02', '1', 'Nhập kho từ phiếu PN4523'),
(102, 'TK028', 'DM01', 'HH016', 'LK02', '1', 'Nhập kho từ phiếu PN4523'),
(103, 'TK029', 'DM01', 'HH013', 'LK02', '1', 'Nhập kho từ phiếu PN4525'),
(104, 'TK030', 'DM11', 'HH0087', 'LK01', '1', 'Nhập kho từ phiếu PN45232'),
(105, 'TK031', 'DM01', 'HH020', 'LK03', '1', 'Nhập kho từ phiếu PN0042');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tonkho_chitiet`
--

CREATE TABLE `tonkho_chitiet` (
  `MaCTTK` int(11) NOT NULL,
  `MaTonKho` char(10) NOT NULL,
  `MaHangHoa` char(10) NOT NULL,
  `ThoiGianNhap` datetime NOT NULL,
  `NgaySanXuat` date DEFAULT NULL,
  `HanSuDung` date DEFAULT NULL,
  `SoLuongTon` int(11) NOT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  `GhiChu` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `tonkho_chitiet`
--

INSERT INTO `tonkho_chitiet` (`MaCTTK`, `MaTonKho`, `MaHangHoa`, `ThoiGianNhap`, `NgaySanXuat`, `HanSuDung`, `SoLuongTon`, `TrangThai`, `GhiChu`) VALUES
(36, 'TK022', 'HH026', '2026-02-03 14:06:00', '2026-02-03', '2026-06-25', 1, 1, NULL),
(37, 'TK020', 'HH027', '2026-02-04 09:52:00', '2026-02-04', '2027-01-27', 23, 1, NULL),
(40, 'TK013', 'HH025', '2026-02-04 10:42:00', '2026-02-04', '2026-09-30', 12, 1, NULL),
(42, 'TK007', 'HH005', '2026-02-04 10:42:00', '2026-02-04', '2026-11-20', 1, 1, NULL),
(43, 'TK024', 'HH018', '2026-02-05 08:33:00', '2026-02-05', '2026-07-28', 6, 1, NULL),
(44, 'TK025', 'HH017', '2026-02-05 08:33:00', '2026-02-05', '2026-07-10', 3, 1, NULL),
(45, 'TK023', 'HH019', '2026-02-05 08:33:00', '2026-02-05', '2026-10-02', 7, 1, NULL),
(46, 'TK026', 'HH015', '2026-02-05 08:35:00', '2025-11-06', '2026-08-21', 12, 1, NULL),
(47, 'TK027', 'HH014', '2026-02-05 08:35:00', '2025-10-28', '2026-08-05', 4, 1, NULL),
(48, 'TK028', 'HH016', '2026-02-05 08:35:00', '2026-02-05', '2026-08-12', 23, 1, NULL),
(49, 'TK014', 'HH002', '2026-02-05 08:38:00', '2026-02-05', '2026-09-17', 5, 1, NULL),
(50, 'TK005', 'HH001', '2026-02-05 08:38:00', '2026-02-02', '2026-05-28', 12, 1, NULL),
(51, 'TK011', 'HH011', '2026-02-05 08:38:00', '2026-02-05', '2026-11-30', 17, 1, NULL),
(52, 'TK029', 'HH013', '2026-02-05 08:38:00', '2025-11-19', '2026-09-05', 9, 1, NULL),
(53, 'TK010', 'HH010', '2026-02-05 08:57:00', '2026-02-02', '2027-01-27', 120, 1, NULL),
(54, 'TK001', 'HH008', '2026-02-05 08:57:00', '2026-01-26', '2027-05-27', 50, 1, NULL),
(55, 'TK030', 'HH0087', '2026-02-05 08:57:00', '2026-02-02', '2027-01-27', 23, 1, NULL),
(56, 'TK013', 'HH025', '2026-03-03 08:21:00', '2026-03-02', '2026-03-04', 23, 1, NULL),
(57, 'TK027', 'HH014', '2026-03-03 08:26:00', NULL, NULL, 3, 1, NULL),
(58, 'TK015', 'HH006', '2026-03-03 08:26:00', NULL, NULL, 1, 1, NULL),
(59, 'TK030', 'HH0087', '2026-03-03 08:27:00', NULL, NULL, 21, 1, NULL),
(60, 'TK031', 'HH020', '2026-03-03 08:27:00', NULL, NULL, 3, 1, NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD PRIMARY KEY (`STT`),
  ADD UNIQUE KEY `UC_ChiTiet` (`MaPhieuNhap`,`MaHangHoa`),
  ADD KEY `FK_ChiTietPhieuNhap_ChiTietHangHoa` (`MaHangHoa`);

--
-- Chỉ mục cho bảng `chitietphieuxuat`
--
ALTER TABLE `chitietphieuxuat`
  ADD PRIMARY KEY (`STT`),
  ADD UNIQUE KEY `UC_ChiTietXuat` (`MaPhieuXuat`,`MaHangHoa`),
  ADD KEY `FK_ChiTietPhieuXuat_ChiTietHangHoa` (`MaHangHoa`);

--
-- Chỉ mục cho bảng `cuochoithoai`
--
ALTER TABLE `cuochoithoai`
  ADD PRIMARY KEY (`MaCHT`);

--
-- Chỉ mục cho bảng `cuochoithoai_thanhvien`
--
ALTER TABLE `cuochoithoai_thanhvien`
  ADD PRIMARY KEY (`MaCHT`,`MaTK`),
  ADD KEY `MaTK` (`MaTK`);

--
-- Chỉ mục cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD PRIMARY KEY (`MaDanhMuc`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_DanhMuc_LoaiDanhMuc` (`MaLoaiDanhMuc`),
  ADD KEY `fk_danhmuc_nhacungcap` (`MaNCC`),
  ADD KEY `fk_danhmuc_nhaphanphoi` (`MaNPP`);

--
-- Chỉ mục cho bảng `donvivanchuyen`
--
ALTER TABLE `donvivanchuyen`
  ADD PRIMARY KEY (`MaDonViVanChuyen`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_DonViVanChuyen_NhaCungCap` (`MaNCC`),
  ADD KEY `FK_DonViVanChuyen_NhaPhanPhoi` (`MaNPP`);

--
-- Chỉ mục cho bảng `hanghoa`
--
ALTER TABLE `hanghoa`
  ADD PRIMARY KEY (`MaHangHoa`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_ChiTietHangHoa_LoaiHangHoa` (`MaLoaiHang`);

--
-- Chỉ mục cho bảng `kehanghoa`
--
ALTER TABLE `kehanghoa`
  ADD PRIMARY KEY (`MaKeHang`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_KeHangHoa_LoaiKho` (`MaLoaiKho`);

--
-- Chỉ mục cho bảng `loaidanhmuc`
--
ALTER TABLE `loaidanhmuc`
  ADD PRIMARY KEY (`MaLoaiDanhMuc`),
  ADD UNIQUE KEY `idx_STT` (`STT`);

--
-- Chỉ mục cho bảng `loaihanghoa`
--
ALTER TABLE `loaihanghoa`
  ADD PRIMARY KEY (`MaLoaiHang`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_LoaiHangHoa_DanhMuc` (`MaDanhMuc`);

--
-- Chỉ mục cho bảng `loaikho`
--
ALTER TABLE `loaikho`
  ADD PRIMARY KEY (`MaLoaiKho`),
  ADD UNIQUE KEY `idx_STT` (`STT`);

--
-- Chỉ mục cho bảng `loainhacungcap`
--
ALTER TABLE `loainhacungcap`
  ADD PRIMARY KEY (`MaLoaiNCC`),
  ADD UNIQUE KEY `idx_STT` (`STT`);

--
-- Chỉ mục cho bảng `loainhanvien`
--
ALTER TABLE `loainhanvien`
  ADD PRIMARY KEY (`MaLoaiNV`),
  ADD UNIQUE KEY `idx_STT` (`STT`);

--
-- Chỉ mục cho bảng `loainhaphanphoi`
--
ALTER TABLE `loainhaphanphoi`
  ADD PRIMARY KEY (`MaLoaiNPP`),
  ADD UNIQUE KEY `idx_STT` (`STT`);

--
-- Chỉ mục cho bảng `luanchuyenhanghoa`
--
ALTER TABLE `luanchuyenhanghoa`
  ADD PRIMARY KEY (`MaLuanChuyen`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_LuanChuyenHangHoa_ChiTietHangHoa` (`MaHangHoa`);

--
-- Chỉ mục cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`MaNCC`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_NhaCungCap_LoaiNCC` (`MaLoaiNCC`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`MaNV`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_NhanVien_LoaiNV` (`MaLoaiNV`);

--
-- Chỉ mục cho bảng `nhaphanphoi`
--
ALTER TABLE `nhaphanphoi`
  ADD PRIMARY KEY (`MaNPP`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_NhaPhanPhoi_LoaiNPP` (`MaLoaiNPP`);

--
-- Chỉ mục cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD PRIMARY KEY (`MaPhieuNhap`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_PhieuNhap_NhanVien` (`MaNhanVien`),
  ADD KEY `FK_PhieuNhap_NhaCungCap` (`MaNCC`);

--
-- Chỉ mục cho bảng `phieuxuat`
--
ALTER TABLE `phieuxuat`
  ADD PRIMARY KEY (`MaPhieuXuat`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_PhieuXuat_NhanVien` (`MaNhanVien`),
  ADD KEY `FK_PhieuXuat_NhaPhanPhoi` (`MaNPP`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`MaTK`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_TaiKhoan_NhanVien` (`MaNV`);

--
-- Chỉ mục cho bảng `thanhly`
--
ALTER TABLE `thanhly`
  ADD PRIMARY KEY (`MaThanhLy`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_ThanhLy_TonKho` (`MaTonKho`),
  ADD KEY `FK_ThanhLy_ChiTietHangHoa` (`MaHangHoa`);

--
-- Chỉ mục cho bảng `tinnhan`
--
ALTER TABLE `tinnhan`
  ADD PRIMARY KEY (`MaTinNhan`),
  ADD KEY `MaCHT` (`MaCHT`),
  ADD KEY `MaTK_Gui` (`MaTK_Gui`);

--
-- Chỉ mục cho bảng `tinnhan_daxem`
--
ALTER TABLE `tinnhan_daxem`
  ADD PRIMARY KEY (`MaTinNhan`,`MaTK`);

--
-- Chỉ mục cho bảng `tonkho`
--
ALTER TABLE `tonkho`
  ADD PRIMARY KEY (`MaTonKho`),
  ADD UNIQUE KEY `idx_STT` (`STT`),
  ADD KEY `FK_TonKho_ChiTietHangHoa` (`MaHangHoa`),
  ADD KEY `FK_TonKho_LoaiKho` (`MaLoaiKho`);

--
-- Chỉ mục cho bảng `tonkho_chitiet`
--
ALTER TABLE `tonkho_chitiet`
  ADD PRIMARY KEY (`MaCTTK`),
  ADD KEY `fk_cttk_tonkho` (`MaTonKho`),
  ADD KEY `fk_cttk_hanghoa` (`MaHangHoa`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT cho bảng `chitietphieuxuat`
--
ALTER TABLE `chitietphieuxuat`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT cho bảng `cuochoithoai`
--
ALTER TABLE `cuochoithoai`
  MODIFY `MaCHT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `donvivanchuyen`
--
ALTER TABLE `donvivanchuyen`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `hanghoa`
--
ALTER TABLE `hanghoa`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `kehanghoa`
--
ALTER TABLE `kehanghoa`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `loaidanhmuc`
--
ALTER TABLE `loaidanhmuc`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `loaihanghoa`
--
ALTER TABLE `loaihanghoa`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `loaikho`
--
ALTER TABLE `loaikho`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `loainhacungcap`
--
ALTER TABLE `loainhacungcap`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `loainhanvien`
--
ALTER TABLE `loainhanvien`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `loainhaphanphoi`
--
ALTER TABLE `loainhaphanphoi`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `luanchuyenhanghoa`
--
ALTER TABLE `luanchuyenhanghoa`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `nhaphanphoi`
--
ALTER TABLE `nhaphanphoi`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT cho bảng `phieuxuat`
--
ALTER TABLE `phieuxuat`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT cho bảng `thanhly`
--
ALTER TABLE `thanhly`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tinnhan`
--
ALTER TABLE `tinnhan`
  MODIFY `MaTinNhan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=436;

--
-- AUTO_INCREMENT cho bảng `tonkho`
--
ALTER TABLE `tonkho`
  MODIFY `STT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT cho bảng `tonkho_chitiet`
--
ALTER TABLE `tonkho_chitiet`
  MODIFY `MaCTTK` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD CONSTRAINT `FK_ChiTietPhieuNhap_ChiTietHangHoa` FOREIGN KEY (`MaHangHoa`) REFERENCES `hanghoa` (`MaHangHoa`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ChiTietPhieuNhap_PhieuNhap` FOREIGN KEY (`MaPhieuNhap`) REFERENCES `phieunhap` (`MaPhieuNhap`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chitietphieuxuat`
--
ALTER TABLE `chitietphieuxuat`
  ADD CONSTRAINT `FK_ChiTietPhieuXuat_ChiTietHangHoa` FOREIGN KEY (`MaHangHoa`) REFERENCES `hanghoa` (`MaHangHoa`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ChiTietPhieuXuat_PhieuXuat` FOREIGN KEY (`MaPhieuXuat`) REFERENCES `phieuxuat` (`MaPhieuXuat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `danhmuc`
--
ALTER TABLE `danhmuc`
  ADD CONSTRAINT `FK_DanhMuc_LoaiDanhMuc` FOREIGN KEY (`MaLoaiDanhMuc`) REFERENCES `loaidanhmuc` (`MaLoaiDanhMuc`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_danhmuc_nhacungcap` FOREIGN KEY (`MaNCC`) REFERENCES `nhacungcap` (`MaNCC`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_danhmuc_nhaphanphoi` FOREIGN KEY (`MaNPP`) REFERENCES `nhaphanphoi` (`MaNPP`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `donvivanchuyen`
--
ALTER TABLE `donvivanchuyen`
  ADD CONSTRAINT `FK_DonViVanChuyen_NhaCungCap` FOREIGN KEY (`MaNCC`) REFERENCES `nhacungcap` (`MaNCC`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DonViVanChuyen_NhaPhanPhoi` FOREIGN KEY (`MaNPP`) REFERENCES `nhaphanphoi` (`MaNPP`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `hanghoa`
--
ALTER TABLE `hanghoa`
  ADD CONSTRAINT `FK_ChiTietHangHoa_LoaiHangHoa` FOREIGN KEY (`MaLoaiHang`) REFERENCES `loaihanghoa` (`MaLoaiHang`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `kehanghoa`
--
ALTER TABLE `kehanghoa`
  ADD CONSTRAINT `FK_KeHangHoa_LoaiKho` FOREIGN KEY (`MaLoaiKho`) REFERENCES `loaikho` (`MaLoaiKho`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `loaihanghoa`
--
ALTER TABLE `loaihanghoa`
  ADD CONSTRAINT `FK_LoaiHangHoa_DanhMuc` FOREIGN KEY (`MaDanhMuc`) REFERENCES `danhmuc` (`MaDanhMuc`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `luanchuyenhanghoa`
--
ALTER TABLE `luanchuyenhanghoa`
  ADD CONSTRAINT `FK_LuanChuyenHangHoa_ChiTietHangHoa` FOREIGN KEY (`MaHangHoa`) REFERENCES `hanghoa` (`MaHangHoa`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD CONSTRAINT `FK_NhaCungCap_LoaiNCC` FOREIGN KEY (`MaLoaiNCC`) REFERENCES `loainhacungcap` (`MaLoaiNCC`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `FK_NhanVien_LoaiNV` FOREIGN KEY (`MaLoaiNV`) REFERENCES `loainhanvien` (`MaLoaiNV`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `nhaphanphoi`
--
ALTER TABLE `nhaphanphoi`
  ADD CONSTRAINT `FK_NhaPhanPhoi_LoaiNPP` FOREIGN KEY (`MaLoaiNPP`) REFERENCES `loainhaphanphoi` (`MaLoaiNPP`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `phieunhap`
--
ALTER TABLE `phieunhap`
  ADD CONSTRAINT `FK_PhieuNhap_NhaCungCap` FOREIGN KEY (`MaNCC`) REFERENCES `nhacungcap` (`MaNCC`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_PhieuNhap_NhanVien` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNV`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `phieuxuat`
--
ALTER TABLE `phieuxuat`
  ADD CONSTRAINT `FK_PhieuXuat_NhaPhanPhoi` FOREIGN KEY (`MaNPP`) REFERENCES `nhaphanphoi` (`MaNPP`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_PhieuXuat_NhanVien` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNV`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `FK_TaiKhoan_NhanVien` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `thanhly`
--
ALTER TABLE `thanhly`
  ADD CONSTRAINT `FK_ThanhLy_ChiTietHangHoa` FOREIGN KEY (`MaHangHoa`) REFERENCES `hanghoa` (`MaHangHoa`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_ThanhLy_TonKho` FOREIGN KEY (`MaTonKho`) REFERENCES `tonkho` (`MaTonKho`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `tonkho`
--
ALTER TABLE `tonkho`
  ADD CONSTRAINT `FK_TonKho_ChiTietHangHoa` FOREIGN KEY (`MaHangHoa`) REFERENCES `hanghoa` (`MaHangHoa`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TonKho_LoaiKho` FOREIGN KEY (`MaLoaiKho`) REFERENCES `loaikho` (`MaLoaiKho`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Các ràng buộc cho bảng `tonkho_chitiet`
--
ALTER TABLE `tonkho_chitiet`
  ADD CONSTRAINT `fk_cttk_hanghoa` FOREIGN KEY (`MaHangHoa`) REFERENCES `hanghoa` (`MaHangHoa`),
  ADD CONSTRAINT `fk_cttk_tonkho` FOREIGN KEY (`MaTonKho`) REFERENCES `tonkho` (`MaTonKho`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
