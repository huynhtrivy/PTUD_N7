<?php
include_once __DIR__ . '/../Model/mPhieuNhap.php';
class clsCPhieuNhap {
    function layDuLieuDonNhapChoNhan() {
        return (new clsMPhieuNhap())->getDonNhapChoNhanDayDu();
    }
    function layDanhSachPhieuNhap() {
        return (new clsMPhieuNhap())->getDanhSachPhieuNhapDayDu();
    }
    function taoMaPhieuPreview() {
        return (new clsMPhieuNhap())->nextCode();
    }
    function luuPhieuNhap($data, $maThuKho) {
        $soDonNhap = trim($data['SoDonNhap'] ?? '');
        if ($soDonNhap === '') {
            return ['ok' => false, 'message' => 'Vui lòng chọn số đơn hàng tham chiếu.'];
        }
        $maHang = $data['MaHang'] ?? [];
        $soLuong = $data['SoLuongNhap'] ?? [];
        $giaNhap = $data['GiaNhap'] ?? [];
        $maKho = $data['MaKho'] ?? [];
        $slProm = $data['SlProm'] ?? [];
        $amtProm = $data['AmtProm'] ?? [];
        $taxDong = $data['TaxAmtDong'] ?? [];
        $rows = [];
        foreach ($maHang as $i => $mh) {
            $mh = trim($mh);
            if ($mh === '') continue;
            $rows[] = [
                'MaHang' => $mh,
                'SoLuongNhap' => (float)($soLuong[$i] ?? 0),
                'GiaNhap' => (float)($giaNhap[$i] ?? 0),
                'MaKho' => trim($maKho[$i] ?? ''),
                'SlProm' => (float)($slProm[$i] ?? 0),
                'AmtProm' => (float)($amtProm[$i] ?? 0),
                'TaxAmtDong' => (float)($taxDong[$i] ?? 0)
            ];
        }
        if (!$rows) {
            return ['ok' => false, 'message' => 'Phiếu nhập phải có ít nhất một mặt hàng.'];
        }
        $header = [
            'MaPhieuNhap' => trim($data['MaPhieuNhap'] ?? ''),
            'SoDonNhap' => $soDonNhap,
            'LoaiDon' => trim($data['LoaiDon'] ?? 'NP'),
            'DiscPercent' => (float)($data['DiscPercent'] ?? 0),
            'PromPercent' => (float)($data['PromPercent'] ?? 0),
            'ComPercent' => (float)($data['ComPercent'] ?? 0),
            'TaxPercent' => (float)($data['TaxPercent'] ?? 0),
            'MaThuKho' => (int)$maThuKho
        ];
        return (new clsMPhieuNhap())->savePhieuNhap($header, $rows);
    }
}
?>