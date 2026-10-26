-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 26, 2026 lúc 04:48 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qlbh7`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdieuchuyen`
--

CREATE TABLE `chitietdieuchuyen` (
  `MaPhieuDC` varchar(20) NOT NULL,
  `MaHang` varchar(20) NOT NULL,
  `SoLuong` decimal(18,2) NOT NULL,
  `DonGia` decimal(18,2) NOT NULL DEFAULT 0.00,
  `ThanhTien` decimal(18,2) NOT NULL DEFAULT 0.00
) ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonban`
--

CREATE TABLE `chitietdonban` (
  `SoDonHang` varchar(20) NOT NULL,
  `MaHang` varchar(20) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` decimal(18,2) NOT NULL,
  `ChietKhauPhanTram` decimal(5,2) NOT NULL DEFAULT 0.00,
  `TienChietKhau` decimal(18,2) NOT NULL DEFAULT 0.00,
  `ThueSuat` decimal(5,2) NOT NULL DEFAULT 0.00,
  `TienThue` decimal(18,2) NOT NULL DEFAULT 0.00,
  `ThanhTien` decimal(18,2) NOT NULL
) ;

--
-- Đang đổ dữ liệu cho bảng `chitietdonban`
--

INSERT INTO `chitietdonban` (`SoDonHang`, `MaHang`, `SoLuong`, `DonGia`, `ChietKhauPhanTram`, `TienChietKhau`, `ThueSuat`, `TienThue`, `ThanhTien`) VALUES
('DH000001', 'H3', 1, 550000.00, 0.00, 0.00, 8.00, 43560.00, 588060.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonnhap`
--

CREATE TABLE `chitietdonnhap` (
  `SoDonNhap` varchar(20) NOT NULL,
  `MaHang` varchar(20) NOT NULL,
  `NhuCau` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TonKhaDung` decimal(18,2) NOT NULL DEFAULT 0.00,
  `DangChoNhan` decimal(18,2) NOT NULL DEFAULT 0.00,
  `SoLuongDat` decimal(18,2) NOT NULL,
  `GiaDuKien` decimal(18,2) NOT NULL,
  `ThanhTien` decimal(18,2) NOT NULL
) ;

--
-- Đang đổ dữ liệu cho bảng `chitietdonnhap`
--

INSERT INTO `chitietdonnhap` (`SoDonNhap`, `MaHang`, `NhuCau`, `TonKhaDung`, `DangChoNhan`, `SoLuongDat`, `GiaDuKien`, `ThanhTien`) VALUES
('DN000001', 'H1', 100.00, 50.00, 0.00, 11.00, 400000.00, 4400000.00),
('DN000001', 'H2', 120.00, 100.00, 0.00, 10.00, 250000.00, 2500000.00),
('DN000002', 'H4', 80.00, 35.00, 0.00, 20.00, 200000.00, 4000000.00),
('DN000002', 'H6', 60.00, 30.00, 0.00, 20.00, 150000.00, 3000000.00),
('DN000003', 'H3', 100.00, 20.00, 0.00, 20.00, 480000.00, 9600000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieunhap`
--

CREATE TABLE `chitietphieunhap` (
  `MaPhieuNhap` varchar(20) NOT NULL,
  `MaHang` varchar(20) NOT NULL,
  `SoLuongNhap` decimal(18,2) NOT NULL,
  `GiaNhap` decimal(18,2) NOT NULL,
  `MaKho` varchar(20) NOT NULL,
  `SlProm` decimal(18,2) NOT NULL DEFAULT 0.00,
  `AmtProm` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TaxAmt` decimal(18,2) NOT NULL DEFAULT 0.00,
  `ThanhTien` decimal(18,2) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuchang`
--

CREATE TABLE `danhmuchang` (
  `MaDanhMuc` varchar(20) NOT NULL,
  `ChungLoai` varchar(120) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `TrangThai` enum('Hoạt động','Ngừng hoạt động') NOT NULL DEFAULT 'Hoạt động',
  `NgayTao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuchang`
--

INSERT INTO `danhmuchang` (`MaDanhMuc`, `ChungLoai`, `MoTa`, `TrangThai`, `NgayTao`) VALUES
('DM001', 'Sữa bột', 'Các sản phẩm sữa bột dinh dưỡng', 'Hoạt động', '2026-10-26 16:24:31'),
('DM002', 'Sữa nước', 'Sữa tươi và sữa tiệt trùng', 'Hoạt động', '2026-10-26 16:24:31'),
('DM003', 'Bánh kẹo', 'Bánh quy và sản phẩm ăn nhẹ', 'Hoạt động', '2026-10-26 16:24:31'),
('DM004', 'Nước giải khát', 'Nước tăng lực, trà đóng chai', 'Hoạt động', '2026-10-26 16:24:31'),
('DM005', 'Ngũ cốc', 'Ngũ cốc dinh dưỡng', 'Hoạt động', '2026-10-26 16:24:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donbanhang`
--

CREATE TABLE `donbanhang` (
  `SoDonHang` varchar(20) NOT NULL,
  `MaKH` varchar(20) NOT NULL,
  `MaNhanVien` int(11) NOT NULL,
  `NgayLap` date NOT NULL,
  `NgayQuaHan` date NOT NULL,
  `TongTienHang` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TongChietKhau` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TongTienThue` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TongTien` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TrangThai` varchar(40) NOT NULL DEFAULT 'Chờ thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donbanhang`
--

INSERT INTO `donbanhang` (`SoDonHang`, `MaKH`, `MaNhanVien`, `NgayLap`, `NgayQuaHan`, `TongTienHang`, `TongChietKhau`, `TongTienThue`, `TongTien`, `TrangThai`) VALUES
('DH000001', 'KH001', 2, '2026-10-26', '2026-11-25', 544500.00, 0.00, 43560.00, 588060.00, 'Chờ thanh toán');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donnhaphang`
--

CREATE TABLE `donnhaphang` (
  `SoDonNhap` varchar(20) NOT NULL,
  `MaNCC` varchar(20) NOT NULL,
  `MaKhoDuKien` varchar(20) NOT NULL,
  `MaChuNPP` int(11) NOT NULL,
  `NgayLap` date NOT NULL,
  `LoaiDon` varchar(10) NOT NULL DEFAULT 'NP',
  `HinhThucThanhToan` varchar(50) NOT NULL,
  `ChietKhauPhanTram` decimal(5,2) NOT NULL DEFAULT 0.00,
  `ThueSuat` decimal(5,2) NOT NULL DEFAULT 0.00,
  `TongTienHang` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TienChietKhau` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TienThue` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TongTien` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TrangThai` varchar(40) NOT NULL DEFAULT 'Chờ nhận hàng',
  `RequestKey` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `donnhaphang`
--

INSERT INTO `donnhaphang` (`SoDonNhap`, `MaNCC`, `MaKhoDuKien`, `MaChuNPP`, `NgayLap`, `LoaiDon`, `HinhThucThanhToan`, `ChietKhauPhanTram`, `ThueSuat`, `TongTienHang`, `TienChietKhau`, `TienThue`, `TongTien`, `TrangThai`, `RequestKey`) VALUES
('DN000001', 'NCC01', 'K1', 5, '2026-10-20', 'NP', 'Trả nợ', 5.00, 10.00, 6900000.00, 345000.00, 655500.00, 7210500.00, 'Đã nhận đủ', 'seed-request-0001'),
('DN000002', 'NCC02', 'K2', 5, '2026-10-22', 'IN', 'Trả ngay', 0.00, 10.00, 7000000.00, 0.00, 700000.00, 7700000.00, 'Đã nhận đủ', 'seed-request-0002'),
('DN000003', 'NCC02', 'K2', 5, '2026-10-26', 'ND', 'Ký gửi', 0.00, 10.00, 9600000.00, 0.00, 960000.00, 10560000.00, 'Chờ nhận hàng', '97fe1743f61e8ea82884e64b');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hanghoa`
--

CREATE TABLE `hanghoa` (
  `MaHang` varchar(20) NOT NULL,
  `TenHang` varchar(180) NOT NULL,
  `MaDanhMuc` varchar(20) NOT NULL,
  `DonViTinh` varchar(30) NOT NULL DEFAULT 'Cái',
  `DonGiaBan` decimal(18,2) NOT NULL DEFAULT 0.00,
  `GiaNhapMacDinh` decimal(18,2) NOT NULL DEFAULT 0.00,
  `ThueSuat` decimal(5,2) NOT NULL DEFAULT 0.00,
  `TrangThai` enum('Hoạt động','Ngừng hoạt động') NOT NULL DEFAULT 'Hoạt động'
) ;

--
-- Đang đổ dữ liệu cho bảng `hanghoa`
--

INSERT INTO `hanghoa` (`MaHang`, `TenHang`, `MaDanhMuc`, `DonViTinh`, `DonGiaBan`, `GiaNhapMacDinh`, `ThueSuat`, `TrangThai`) VALUES
('H1', 'Sữa bột Alpha Gold', 'DM001', 'Thùng', 4500000.00, 4000000.00, 10.00, 'Hoạt động'),
('H2', 'Sữa tươi tiệt trùng', 'DM002', 'Thùng', 300000.00, 250000.00, 8.00, 'Hoạt động'),
('H3', 'Bánh quy bơ', 'DM003', 'Thùng', 550000.00, 480000.00, 8.00, 'Hoạt động'),
('H4', 'Nước tăng lực', 'DM004', 'Thùng', 230000.00, 200000.00, 10.00, 'Hoạt động'),
('H5', 'Ngũ cốc dinh dưỡng', 'DM005', 'Thùng', 1550000.00, 1400000.00, 10.00, 'Hoạt động'),
('H6', 'Trà xanh đóng chai', 'DM004', 'Thùng', 180000.00, 150000.00, 10.00, 'Hoạt động');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `MaKH` varchar(20) NOT NULL,
  `TenKH` varchar(150) NOT NULL,
  `DienThoai` varchar(20) DEFAULT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `HanNo` int(11) NOT NULL DEFAULT 30,
  `HanMucNo` decimal(18,2) NOT NULL DEFAULT 0.00,
  `NoTrongHan` decimal(18,2) NOT NULL DEFAULT 0.00,
  `NoQuaHan` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TrangThai` enum('Hoạt động','Ngừng hoạt động') NOT NULL DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`MaKH`, `TenKH`, `DienThoai`, `DiaChi`, `HanNo`, `HanMucNo`, `NoTrongHan`, `NoQuaHan`, `TrangThai`) VALUES
('KH001', 'Cửa hàng An Phát', '0909000001', 'Quận 1, TP.HCM', 30, 50000000.00, 2588060.00, 0.00, 'Hoạt động'),
('KH002', 'Siêu thị Minh Long', '0909000002', 'Thủ Đức, TP.HCM', 15, 30000000.00, 500000.00, 100000.00, 'Hoạt động'),
('KH003', 'Đại lý Hòa Bình', '0909000003', 'Bình Thạnh, TP.HCM', 20, 40000000.00, 0.00, 0.00, 'Hoạt động');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kho`
--

CREATE TABLE `kho` (
  `MaKho` varchar(20) NOT NULL,
  `TenKho` varchar(120) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `TrangThai` enum('Hoạt động','Ngừng hoạt động') NOT NULL DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kho`
--

INSERT INTO `kho` (`MaKho`, `TenKho`, `DiaChi`, `TrangThai`) VALUES
('K1', 'Kho Tổng', 'TP.HCM', 'Hoạt động'),
('K2', 'Kho HCM', 'Quận 7, TP.HCM', 'Hoạt động'),
('K3', 'Kho Đồng Nai', 'Biên Hòa, Đồng Nai', 'Hoạt động');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacungcap`
--

CREATE TABLE `nhacungcap` (
  `MaNCC` varchar(20) NOT NULL,
  `TenNCC` varchar(180) NOT NULL,
  `DienThoai` varchar(20) DEFAULT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `TrangThai` enum('Hoạt động','Ngừng hoạt động') NOT NULL DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhacungcap`
--

INSERT INTO `nhacungcap` (`MaNCC`, `TenNCC`, `DienThoai`, `DiaChi`, `TrangThai`) VALUES
('NCC01', 'Công ty TNHH Sữa Việt Nam (Vinamilk)', '02838286688', 'TP.HCM', 'Hoạt động'),
('NCC02', 'Công ty TNHH Nestles Việt Nam', '02839113737', 'Đồng Nai', 'Hoạt động'),
('NCC03', 'Công ty Cổ phần Thực phẩm Quốc tế (Interfood)', '02513836232', 'Đồng Nai', 'Hoạt động');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhomquyen`
--

CREATE TABLE `nhomquyen` (
  `MaNhomQuyen` int(11) NOT NULL,
  `MaQuyen` varchar(30) NOT NULL,
  `TenNhomQuyen` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhomquyen`
--

INSERT INTO `nhomquyen` (`MaNhomQuyen`, `MaQuyen`, `TenNhomQuyen`) VALUES
(1, 'ADMIN', 'Admin'),
(2, 'NHANVIEN', 'Nhân viên bán hàng'),
(3, 'KETOAN', 'Kế toán'),
(4, 'THUKHO', 'Thủ kho'),
(5, 'CHUNPP', 'Chủ NPP');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieudieuchuyen`
--

CREATE TABLE `phieudieuchuyen` (
  `MaPhieuDC` varchar(20) NOT NULL,
  `MaKhoXuat` varchar(20) NOT NULL,
  `MaKhoNhap` varchar(20) NOT NULL,
  `MaThuKho` int(11) NOT NULL,
  `NgayDieuChuyen` date NOT NULL,
  `GhiChu` varchar(500) DEFAULT NULL,
  `TongGiaTri` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TrangThai` varchar(40) NOT NULL DEFAULT 'Đã duyệt'
) ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhapkho`
--

CREATE TABLE `phieunhapkho` (
  `MaPhieuNhap` varchar(20) NOT NULL,
  `SoDonNhap` varchar(20) NOT NULL,
  `LoaiDon` varchar(10) NOT NULL,
  `NgayLap` date NOT NULL,
  `MaThuKho` int(11) NOT NULL,
  `DiscAmt` decimal(18,2) NOT NULL DEFAULT 0.00,
  `PromAmt` decimal(18,2) NOT NULL DEFAULT 0.00,
  `ComAmt` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TaxAmt` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TotalAmt` decimal(18,2) NOT NULL DEFAULT 0.00,
  `TrangThai` varchar(40) NOT NULL DEFAULT 'Đã duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `MaTaiKhoan` int(11) NOT NULL,
  `TenDangNhap` varchar(60) NOT NULL,
  `MatKhau` varchar(64) NOT NULL,
  `HoTen` varchar(120) NOT NULL,
  `MaNhomQuyen` int(11) NOT NULL,
  `TrangThai` enum('Hoạt động','Khóa') NOT NULL DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`MaTaiKhoan`, `TenDangNhap`, `MatKhau`, `HoTen`, `MaNhomQuyen`, `TrangThai`) VALUES
(1, 'admin', '827ccb0eea8a706c4c34a16891f84e7b', 'Quản trị hệ thống', 1, 'Hoạt động'),
(2, 'nhanvien_01', '827ccb0eea8a706c4c34a16891f84e7b', 'Nguyễn Minh Sales', 2, 'Hoạt động'),
(3, 'ketoan_lan', '827ccb0eea8a706c4c34a16891f84e7b', 'Nguyễn Thị Lan', 3, 'Hoạt động'),
(4, 'thukho_nam', '827ccb0eea8a706c4c34a16891f84e7b', 'Trần Văn Nam', 4, 'Hoạt động'),
(5, 'chunpp_01', '827ccb0eea8a706c4c34a16891f84e7b', 'Chủ NPP Thành Shop', 5, 'Hoạt động');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tonkho`
--

CREATE TABLE `tonkho` (
  `MaKho` varchar(20) NOT NULL,
  `MaHang` varchar(20) NOT NULL,
  `SoLuongTon` decimal(18,2) NOT NULL DEFAULT 0.00,
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Đang đổ dữ liệu cho bảng `tonkho`
--

INSERT INTO `tonkho` (`MaKho`, `MaHang`, `SoLuongTon`, `CapNhatLuc`) VALUES
('K1', 'H1', 61.00, '2026-10-26 18:03:30'),
('K1', 'H2', 110.00, '2026-10-26 18:03:30'),
('K1', 'H3', 40.00, '2026-10-26 16:24:31'),
('K1', 'H4', 80.00, '2026-10-26 16:24:31'),
('K1', 'H5', 25.00, '2026-10-26 16:24:31'),
('K1', 'H6', 60.00, '2026-10-26 16:24:31'),
('K2', 'H1', 15.00, '2026-10-26 16:24:31'),
('K2', 'H2', 50.00, '2026-10-26 16:24:31'),
('K2', 'H3', 20.00, '2026-10-26 16:24:31'),
('K2', 'H4', 74.98, '2026-10-26 19:03:52'),
('K2', 'H5', 10.00, '2026-10-26 16:24:31'),
('K2', 'H6', 70.00, '2026-10-26 19:04:03'),
('K3', 'H1', 10.00, '2026-10-26 16:24:31'),
('K3', 'H2', 30.00, '2026-10-26 16:24:31'),
('K3', 'H3', 15.00, '2026-10-26 16:24:31'),
('K3', 'H4', 20.00, '2026-10-26 16:24:31'),
('K3', 'H5', 8.00, '2026-10-26 16:24:31'),
('K3', 'H6', 20.00, '2026-10-26 16:24:31');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitietdieuchuyen`
--
ALTER TABLE `chitietdieuchuyen`
  ADD PRIMARY KEY (`MaPhieuDC`,`MaHang`),
  ADD KEY `FK_CTDC_Hang` (`MaHang`);

--
-- Chỉ mục cho bảng `chitietdonban`
--
ALTER TABLE `chitietdonban`
  ADD PRIMARY KEY (`SoDonHang`,`MaHang`),
  ADD KEY `FK_CTDonBan_Hang` (`MaHang`);

--
-- Chỉ mục cho bảng `chitietdonnhap`
--
ALTER TABLE `chitietdonnhap`
  ADD PRIMARY KEY (`SoDonNhap`,`MaHang`),
  ADD KEY `FK_CTDonNhap_Hang` (`MaHang`);

--
-- Chỉ mục cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD PRIMARY KEY (`MaPhieuNhap`,`MaHang`),
  ADD KEY `FK_CTPhieuNhap_Hang` (`MaHang`),
  ADD KEY `FK_CTPhieuNhap_Kho` (`MaKho`);

--
-- Chỉ mục cho bảng `danhmuchang`
--
ALTER TABLE `danhmuchang`
  ADD PRIMARY KEY (`MaDanhMuc`),
  ADD UNIQUE KEY `ChungLoai` (`ChungLoai`);

--
-- Chỉ mục cho bảng `donbanhang`
--
ALTER TABLE `donbanhang`
  ADD PRIMARY KEY (`SoDonHang`),
  ADD KEY `FK_DonBan_KhachHang` (`MaKH`),
  ADD KEY `FK_DonBan_NhanVien` (`MaNhanVien`);

--
-- Chỉ mục cho bảng `donnhaphang`
--
ALTER TABLE `donnhaphang`
  ADD PRIMARY KEY (`SoDonNhap`),
  ADD UNIQUE KEY `RequestKey` (`RequestKey`),
  ADD KEY `FK_DonNhap_NCC` (`MaNCC`),
  ADD KEY `FK_DonNhap_Kho` (`MaKhoDuKien`),
  ADD KEY `FK_DonNhap_ChuNPP` (`MaChuNPP`);

--
-- Chỉ mục cho bảng `hanghoa`
--
ALTER TABLE `hanghoa`
  ADD PRIMARY KEY (`MaHang`),
  ADD KEY `FK_HangHoa_DanhMuc` (`MaDanhMuc`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`MaKH`);

--
-- Chỉ mục cho bảng `kho`
--
ALTER TABLE `kho`
  ADD PRIMARY KEY (`MaKho`);

--
-- Chỉ mục cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`MaNCC`);

--
-- Chỉ mục cho bảng `nhomquyen`
--
ALTER TABLE `nhomquyen`
  ADD PRIMARY KEY (`MaNhomQuyen`),
  ADD UNIQUE KEY `MaQuyen` (`MaQuyen`),
  ADD UNIQUE KEY `TenNhomQuyen` (`TenNhomQuyen`);

--
-- Chỉ mục cho bảng `phieudieuchuyen`
--
ALTER TABLE `phieudieuchuyen`
  ADD PRIMARY KEY (`MaPhieuDC`),
  ADD KEY `FK_DC_KhoXuat` (`MaKhoXuat`),
  ADD KEY `FK_DC_KhoNhap` (`MaKhoNhap`),
  ADD KEY `FK_DC_ThuKho` (`MaThuKho`);

--
-- Chỉ mục cho bảng `phieunhapkho`
--
ALTER TABLE `phieunhapkho`
  ADD PRIMARY KEY (`MaPhieuNhap`),
  ADD KEY `FK_PhieuNhap_DonNhap` (`SoDonNhap`),
  ADD KEY `FK_PhieuNhap_ThuKho` (`MaThuKho`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`MaTaiKhoan`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`),
  ADD KEY `FK_TaiKhoan_NhomQuyen` (`MaNhomQuyen`);

--
-- Chỉ mục cho bảng `tonkho`
--
ALTER TABLE `tonkho`
  ADD PRIMARY KEY (`MaKho`,`MaHang`),
  ADD KEY `FK_TonKho_HangHoa` (`MaHang`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `nhomquyen`
--
ALTER TABLE `nhomquyen`
  MODIFY `MaNhomQuyen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `MaTaiKhoan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitietdieuchuyen`
--
ALTER TABLE `chitietdieuchuyen`
  ADD CONSTRAINT `FK_CTDC_Hang` FOREIGN KEY (`MaHang`) REFERENCES `hanghoa` (`MaHang`),
  ADD CONSTRAINT `FK_CTDC_Phieu` FOREIGN KEY (`MaPhieuDC`) REFERENCES `phieudieuchuyen` (`MaPhieuDC`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `chitietdonban`
--
ALTER TABLE `chitietdonban`
  ADD CONSTRAINT `FK_CTDonBan_Don` FOREIGN KEY (`SoDonHang`) REFERENCES `donbanhang` (`SoDonHang`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_CTDonBan_Hang` FOREIGN KEY (`MaHang`) REFERENCES `hanghoa` (`MaHang`);

--
-- Các ràng buộc cho bảng `chitietdonnhap`
--
ALTER TABLE `chitietdonnhap`
  ADD CONSTRAINT `FK_CTDonNhap_Don` FOREIGN KEY (`SoDonNhap`) REFERENCES `donnhaphang` (`SoDonNhap`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_CTDonNhap_Hang` FOREIGN KEY (`MaHang`) REFERENCES `hanghoa` (`MaHang`);

--
-- Các ràng buộc cho bảng `chitietphieunhap`
--
ALTER TABLE `chitietphieunhap`
  ADD CONSTRAINT `FK_CTPhieuNhap_Hang` FOREIGN KEY (`MaHang`) REFERENCES `hanghoa` (`MaHang`),
  ADD CONSTRAINT `FK_CTPhieuNhap_Kho` FOREIGN KEY (`MaKho`) REFERENCES `kho` (`MaKho`),
  ADD CONSTRAINT `FK_CTPhieuNhap_Phieu` FOREIGN KEY (`MaPhieuNhap`) REFERENCES `phieunhapkho` (`MaPhieuNhap`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `donbanhang`
--
ALTER TABLE `donbanhang`
  ADD CONSTRAINT `FK_DonBan_KhachHang` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`),
  ADD CONSTRAINT `FK_DonBan_NhanVien` FOREIGN KEY (`MaNhanVien`) REFERENCES `taikhoan` (`MaTaiKhoan`);

--
-- Các ràng buộc cho bảng `donnhaphang`
--
ALTER TABLE `donnhaphang`
  ADD CONSTRAINT `FK_DonNhap_ChuNPP` FOREIGN KEY (`MaChuNPP`) REFERENCES `taikhoan` (`MaTaiKhoan`),
  ADD CONSTRAINT `FK_DonNhap_Kho` FOREIGN KEY (`MaKhoDuKien`) REFERENCES `kho` (`MaKho`),
  ADD CONSTRAINT `FK_DonNhap_NCC` FOREIGN KEY (`MaNCC`) REFERENCES `nhacungcap` (`MaNCC`);

--
-- Các ràng buộc cho bảng `hanghoa`
--
ALTER TABLE `hanghoa`
  ADD CONSTRAINT `FK_HangHoa_DanhMuc` FOREIGN KEY (`MaDanhMuc`) REFERENCES `danhmuchang` (`MaDanhMuc`);

--
-- Các ràng buộc cho bảng `phieudieuchuyen`
--
ALTER TABLE `phieudieuchuyen`
  ADD CONSTRAINT `FK_DC_KhoNhap` FOREIGN KEY (`MaKhoNhap`) REFERENCES `kho` (`MaKho`),
  ADD CONSTRAINT `FK_DC_KhoXuat` FOREIGN KEY (`MaKhoXuat`) REFERENCES `kho` (`MaKho`),
  ADD CONSTRAINT `FK_DC_ThuKho` FOREIGN KEY (`MaThuKho`) REFERENCES `taikhoan` (`MaTaiKhoan`);

--
-- Các ràng buộc cho bảng `phieunhapkho`
--
ALTER TABLE `phieunhapkho`
  ADD CONSTRAINT `FK_PhieuNhap_DonNhap` FOREIGN KEY (`SoDonNhap`) REFERENCES `donnhaphang` (`SoDonNhap`),
  ADD CONSTRAINT `FK_PhieuNhap_ThuKho` FOREIGN KEY (`MaThuKho`) REFERENCES `taikhoan` (`MaTaiKhoan`);

--
-- Các ràng buộc cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `FK_TaiKhoan_NhomQuyen` FOREIGN KEY (`MaNhomQuyen`) REFERENCES `nhomquyen` (`MaNhomQuyen`);

--
-- Các ràng buộc cho bảng `tonkho`
--
ALTER TABLE `tonkho`
  ADD CONSTRAINT `FK_TonKho_HangHoa` FOREIGN KEY (`MaHang`) REFERENCES `hanghoa` (`MaHang`),
  ADD CONSTRAINT `FK_TonKho_Kho` FOREIGN KEY (`MaKho`) REFERENCES `kho` (`MaKho`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
