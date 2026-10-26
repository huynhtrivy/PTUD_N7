<?php
include_once __DIR__ . '/../Model/mDonBan.php';

class clsCDonBan {
    function layKhachHang() { return (new clsMDonBan())->getKhachHang(); }
    function layHangHoa() { return (new clsMDonBan())->getHangHoa(); }
    function layDonGanDay() { return (new clsMDonBan())->getDonGanDay(); }
    function taoMaDonPreview() { return (new clsMDonBan())->nextCode(); }

    function luuDonBan($data, $maNhanVien) {
        $maKH = trim($data['MaKH'] ?? '');
        if ($maKH === '') return ['ok' => false, 'message' => 'Vui lòng chọn khách hàng.'];
        $maHang = $data['MaHang'] ?? [];
        $soLuong = $data['SoLuong'] ?? [];
        $donGia = $data['DonGia'] ?? [];
        $ck = $data['ChietKhau'] ?? [];
        $thue = $data['ThueSuat'] ?? [];
        $rows = [];
        foreach ($maHang as $i => $mh) {
            $mh = trim($mh);
            if ($mh === '') continue;
            $rows[] = [
                'MaHang' => $mh,
                'SoLuong' => (float)($soLuong[$i] ?? 0),
                'DonGia' => (float)($donGia[$i] ?? 0),
                'ChietKhau' => (float)($ck[$i] ?? 0),
                'ThueSuat' => (float)($thue[$i] ?? 0),
            ];
        }
        if (!$rows) return ['ok' => false, 'message' => 'Đơn bán hàng phải có ít nhất một mặt hàng.'];
        return (new clsMDonBan())->saveDonBan([
            'SoDonHang' => trim($data['SoDonHang'] ?? ''),
            'MaKH' => $maKH,
            'MaNhanVien' => $maNhanVien,
        ], $rows);
    }
}
