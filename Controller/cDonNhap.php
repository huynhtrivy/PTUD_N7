<?php
include_once __DIR__ . '/../Model/mDonNhap.php';

class clsCDonNhap {
    function layNhaCungCap() { return (new clsMDonNhap())->getNhaCungCap(); }
    function layKho() { return (new clsMDonNhap())->getKho(); }
    function layHangHoa() { return (new clsMDonNhap())->getHangHoa(); }
    function layTonKho() { return (new clsMDonNhap())->getTonKho(); }
    function layDangChoNhan() { return (new clsMDonNhap())->getDangChoNhan(); }
    function layDonGanDay() { return (new clsMDonNhap())->getDonGanDay(); }
    function taoMaPreview() { return (new clsMDonNhap())->nextCode(); }

    function luuDon($data, $maChuNPP) {
        $maNCC = trim($data['MaNCC'] ?? '');
        $maKho = trim($data['MaKhoDuKien'] ?? '');
        if ($maNCC === '' || $maKho === '') return ['ok' => false, 'message' => 'Vui lòng chọn nhà cung cấp và kho dự kiến nhận.'];
        $maHang = $data['MaHang'] ?? [];
        $nhuCau = $data['NhuCau'] ?? [];
        $soLuongDat = $data['SoLuongDat'] ?? [];
        $gia = $data['GiaDuKien'] ?? [];
        $rows = [];
        foreach ($maHang as $i => $mh) {
            $mh = trim($mh);
            if ($mh === '') continue;
            $rows[] = [
                'MaHang' => $mh,
                'NhuCau' => (float)($nhuCau[$i] ?? 0),
                'SoLuongDat' => (float)($soLuongDat[$i] ?? 0),
                'GiaDuKien' => (float)($gia[$i] ?? 0),
            ];
        }
        if (!$rows) return ['ok' => false, 'message' => 'Đơn đặt nhập phải có ít nhất một mặt hàng.'];
        return (new clsMDonNhap())->saveDon([
            'SoDonNhap' => trim($data['SoDonNhap'] ?? ''),
            'MaNCC' => $maNCC,
            'MaKhoDuKien' => $maKho,
            'MaChuNPP' => $maChuNPP,
            'HinhThucThanhToan' => trim($data['HinhThucThanhToan'] ?? 'Trả nợ'),
            'ChietKhauPhanTram' => (float)($data['ChietKhauPhanTram'] ?? 0),
            'ThueSuat' => (float)($data['ThueSuat'] ?? 0),
        ], $rows);
    }
}
