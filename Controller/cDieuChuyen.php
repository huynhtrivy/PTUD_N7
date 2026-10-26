<?php
include_once __DIR__ . '/../Model/mDieuChuyen.php';

class clsCDieuChuyen {
    function layKho() { return (new clsMDieuChuyen())->getKho(); }
    function layHangVaTon() { return (new clsMDieuChuyen())->getHangVaTon(); }
    function layPhieuGanDay() { return (new clsMDieuChuyen())->getPhieuGanDay(); }
    function taoMaPreview() { return (new clsMDieuChuyen())->nextCode(); }

    function luuPhieu($data, $maThuKho) {
        $khoXuat = trim($data['MaKhoXuat'] ?? '');
        $khoNhap = trim($data['MaKhoNhap'] ?? '');
        if ($khoXuat === '' || $khoNhap === '') return ['ok' => false, 'message' => 'Vui lòng chọn kho xuất và kho nhập.'];
        if ($khoXuat === $khoNhap) return ['ok' => false, 'message' => 'Kho xuất và kho nhập không được trùng nhau.'];
        $maHang = $data['MaHang'] ?? [];
        $soLuong = $data['SoLuong'] ?? [];
        $donGia = $data['DonGia'] ?? [];
        $rows = [];
        foreach ($maHang as $i => $mh) {
            $mh = trim($mh);
            if ($mh === '') continue;
            $rows[] = ['MaHang' => $mh, 'SoLuong' => (float)($soLuong[$i] ?? 0), 'DonGia' => (float)($donGia[$i] ?? 0)];
        }
        if (!$rows) return ['ok' => false, 'message' => 'Phiếu điều chuyển phải có ít nhất một mặt hàng.'];
        return (new clsMDieuChuyen())->savePhieu([
            'MaPhieuDC' => trim($data['MaPhieuDC'] ?? ''),
            'MaKhoXuat' => $khoXuat,
            'MaKhoNhap' => $khoNhap,
            'MaThuKho' => $maThuKho,
            'GhiChu' => trim($data['GhiChu'] ?? ''),
        ], $rows);
    }
}
