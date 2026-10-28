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
        $key = $data['RequestKey'] ?? '';
        if (!is_string($key) || !preg_match('/^[a-f0-9]{48}$/D', $key)
            || !isset($_SESSION['purchase_requests'][$key])) {
            return ['ok' => false, 'message' => 'Phiên lập đơn không hợp lệ. Vui lòng mở lại biểu mẫu.'];
        }
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
            foreach ([$nhuCau[$i] ?? null, $soLuongDat[$i] ?? null, $gia[$i] ?? null] as $number) {
                if (!is_scalar($number) || !is_numeric($number) || !is_finite((float)$number)) {
                    return ['ok' => false, 'message' => 'Nhu cầu, số lượng và giá phải là số hợp lệ.'];
                }
            }
            $rows[] = [
                'MaHang' => $mh,
                'NhuCau' => (float)($nhuCau[$i] ?? 0),
                'SoLuongDat' => (float)($soLuongDat[$i] ?? 0),
                'GiaDuKien' => (float)($gia[$i] ?? 0),
            ];
        }
        if (!$rows) return ['ok' => false, 'message' => 'Đơn đặt nhập phải có ít nhất một mặt hàng.'];
        foreach (['ChietKhauPhanTram', 'ThueSuat'] as $field) {
            if (!is_numeric($data[$field] ?? null) || !is_finite((float)$data[$field])) {
                return ['ok' => false, 'message' => 'Chiết khấu và thuế phải là số hợp lệ.'];
            }
        }
        if (!in_array($data['HinhThucThanhToan'] ?? '', ['Trả ngay', 'Trả nợ', 'Ký gửi'], true)) {
            return ['ok' => false, 'message' => 'Hình thức thanh toán dự kiến không hợp lệ.'];
        }
        return (new clsMDonNhap())->saveDon([
            'RequestKey' => $key,
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
