<?php
include_once __DIR__ . '/mKetnoi.php';
class clsMPhieuNhap {
    function nextCode() {
        $p = new clsKetnoi();
        $conn = $p->moketnoi();
        $code = $this->nextCodeWithConn($conn);
        $p->dongketnoi($conn);
        return $code;
    }
    private function nextCodeWithConn($conn) {
        $prefix = 'PN-' . date('ymd') . '-';
        $like = $prefix . '%';
        $sql = "SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(MaPhieuNhap,'-',-1) AS UNSIGNED)),0)+1 AS n FROM PhieuNhapKho WHERE MaPhieuNhap LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $n = (int)$stmt->get_result()->fetch_assoc()['n'];
        $stmt->close();
        return $prefix . str_pad($n, 3, '0', STR_PAD_LEFT);
    }
    function getDonNhapChoNhanDayDu() {
        $p = new clsKetnoi();
        $conn = $p->moketnoi();
        $sql = "SELECT dn.SoDonNhap,dn.LoaiDon,dn.NgayLap,dn.MaKhoDuKien,dn.ChietKhauPhanTram,dn.ThueSuat,dn.HinhThucThanhToan,ncc.MaNCC,ncc.TenNCC,ncc.DienThoai,k.TenKho,ct.MaHang,hh.TenHang,ct.SoLuongDat,ct.GiaDuKien,COALESCE((SELECT SUM(ctpn.SoLuongNhap) FROM PhieuNhapKho pn JOIN ChiTietPhieuNhap ctpn ON ctpn.MaPhieuNhap=pn.MaPhieuNhap WHERE pn.SoDonNhap=dn.SoDonNhap AND pn.TrangThai='Đã duyệt' AND ctpn.MaHang=ct.MaHang),0) AS DaNhap FROM DonNhapHang dn JOIN NhaCungCap ncc ON ncc.MaNCC=dn.MaNCC JOIN Kho k ON k.MaKho=dn.MaKhoDuKien JOIN ChiTietDonNhap ct ON ct.SoDonNhap=dn.SoDonNhap JOIN HangHoa hh ON hh.MaHang=ct.MaHang WHERE dn.TrangThai IN ('Chờ nhận hàng','Đã nhận một phần') ORDER BY dn.NgayLap DESC,dn.SoDonNhap,ct.MaHang";
        $rs = $conn->query($sql);
        $orders = [];
        while ($row = $rs->fetch_assoc()) {
            $conLai = (float)$row['SoLuongDat'] - (float)$row['DaNhap'];
            if ($conLai <= 0) continue;
            $soDon = $row['SoDonNhap'];
            if (!isset($orders[$soDon])) {
                $orders[$soDon] = [
                    'SoDonNhap' => $soDon,
                    'LoaiDon' => $row['LoaiDon'],
                    'NgayLap' => $row['NgayLap'],
                    'MaKhoDuKien' => $row['MaKhoDuKien'],
                    'TenKho' => $row['TenKho'],
                    'MaNCC' => $row['MaNCC'],
                    'TenNCC' => $row['TenNCC'],
                    'DienThoaiNCC' => $row['DienThoai'],
                    'ChietKhauPhanTram' => (float)$row['ChietKhauPhanTram'],
                    'ThueSuat' => (float)$row['ThueSuat'],
                    'HinhThucThanhToan' => $row['HinhThucThanhToan'],
                    'chiTiet' => []
                ];
            }
            $orders[$soDon]['chiTiet'][] = [
                'MaHang' => $row['MaHang'],
                'TenHang' => $row['TenHang'],
                'SoLuongDat' => (float)$row['SoLuongDat'],
                'DaNhap' => (float)$row['DaNhap'],
                'ConLai' => $conLai,
                'GiaDuKien' => (float)$row['GiaDuKien']
            ];
        }
        $p->dongketnoi($conn);
        return array_values($orders);
    }
    function getDanhSachPhieuNhapDayDu() {
        $p = new clsKetnoi();
        $conn = $p->moketnoi();
        $sql = "SELECT pn.MaPhieuNhap,pn.SoDonNhap,pn.LoaiDon,pn.NgayLap,pn.DiscAmt,pn.PromAmt,pn.ComAmt,pn.TaxAmt,pn.TotalAmt,pn.TrangThai,ncc.TenNCC,k.MaKho,k.TenKho FROM PhieuNhapKho pn JOIN DonNhapHang dn ON dn.SoDonNhap=pn.SoDonNhap JOIN NhaCungCap ncc ON ncc.MaNCC=dn.MaNCC JOIN Kho k ON k.MaKho=dn.MaKhoDuKien ORDER BY pn.NgayLap DESC,pn.MaPhieuNhap DESC LIMIT 5";
        $headers = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
        if (!$headers) {
            $p->dongketnoi($conn);
            return [];
        }
        $result = [];
        foreach ($headers as $header) {
            $header['DiscAmt'] = (float)$header['DiscAmt'];
            $header['PromAmt'] = (float)$header['PromAmt'];
            $header['ComAmt'] = (float)$header['ComAmt'];
            $header['TaxAmt'] = (float)$header['TaxAmt'];
            $header['TotalAmt'] = (float)$header['TotalAmt'];
            $header['chiTiet'] = [];
            $stmt = $conn->prepare("SELECT ct.MaHang,hh.TenHang,ct.SoLuongNhap,ct.GiaNhap,ct.MaKho,ct.SlProm,ct.AmtProm,ct.TaxAmt,ct.ThanhTien FROM ChiTietPhieuNhap ct JOIN HangHoa hh ON hh.MaHang=ct.MaHang WHERE ct.MaPhieuNhap=? ORDER BY ct.MaHang");
            $stmt->bind_param('s', $header['MaPhieuNhap']);
            $stmt->execute();
            $rs = $stmt->get_result();
            while ($row = $rs->fetch_assoc()) {
                $row['SoLuongNhap'] = (float)$row['SoLuongNhap'];
                $row['GiaNhap'] = (float)$row['GiaNhap'];
                $row['SlProm'] = (float)$row['SlProm'];
                $row['AmtProm'] = (float)$row['AmtProm'];
                $row['TaxAmt'] = (float)$row['TaxAmt'];
                $row['ThanhTien'] = (float)$row['ThanhTien'];
                $header['chiTiet'][] = $row;
            }
            $stmt->close();
            $result[] = $header;
        }
        $p->dongketnoi($conn);
        return $result;
    }
    function savePhieuNhap($header, $rows) {
        $p = new clsKetnoi();
        $conn = null;
        try {
            $conn = $p->moketnoi();
            $conn->begin_transaction();
            foreach (['DiscPercent','PromPercent','ComPercent','TaxPercent'] as $field) {
                if ($header[$field] < 0 || $header[$field] > 100) {
                    throw new Exception('Các tỷ lệ phần trăm phải từ 0 đến 100.');
                }
            }
            $stmt = $conn->prepare("SELECT SoDonNhap,MaKhoDuKien,TrangThai FROM DonNhapHang WHERE SoDonNhap=? FOR UPDATE");
            $stmt->bind_param('s', $header['SoDonNhap']);
            $stmt->execute();
            $order = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$order) throw new Exception('Không tìm thấy đơn đặt nhập.');
            if (!in_array($order['TrangThai'], ['Chờ nhận hàng','Đã nhận một phần'])) {
                throw new Exception('Đơn nhập không còn ở trạng thái có thể nhận hàng.');
            }
            if (!$rows) throw new Exception('Phiếu nhập chưa có mặt hàng.');
            $seen = [];
            $totalGoods = 0;
            foreach ($rows as &$row) {
                if ($row['SoLuongNhap'] <= 0) throw new Exception('Số lượng nhập phải lớn hơn 0.');
                if ($row['GiaNhap'] <= 0) throw new Exception('Giá nhập phải lớn hơn 0.');
                if ($row['SlProm'] < 0 || $row['AmtProm'] < 0 || $row['TaxAmtDong'] < 0) {
                    throw new Exception('Số lượng KM, tiền KM và tiền thuế không được âm.');
                }
                if (isset($seen[$row['MaHang']])) {
                    throw new Exception('Mặt hàng ' . $row['MaHang'] . ' bị trùng.');
                }
                $seen[$row['MaHang']] = true;
                if (!$row['MaKho']) $row['MaKho'] = $order['MaKhoDuKien'];
                if ($row['MaKho'] !== $order['MaKhoDuKien']) {
                    throw new Exception('Kho nhập không đúng kho dự kiến.');
                }
                $stmt = $conn->prepare("SELECT SoLuongDat FROM ChiTietDonNhap WHERE SoDonNhap=? AND MaHang=? FOR UPDATE");
                $stmt->bind_param('ss', $header['SoDonNhap'], $row['MaHang']);
                $stmt->execute();
                $detail = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if (!$detail) {
                    throw new Exception('Mặt hàng ' . $row['MaHang'] . ' không thuộc đơn nhập.');
                }
                $stmt = $conn->prepare("SELECT COALESCE(SUM(ct.SoLuongNhap),0) AS DaNhap FROM PhieuNhapKho pn JOIN ChiTietPhieuNhap ct ON ct.MaPhieuNhap=pn.MaPhieuNhap WHERE pn.SoDonNhap=? AND pn.TrangThai='Đã duyệt' AND ct.MaHang=?");
                $stmt->bind_param('ss', $header['SoDonNhap'], $row['MaHang']);
                $stmt->execute();
                $daNhap = (float)$stmt->get_result()->fetch_assoc()['DaNhap'];
                $stmt->close();
                if ($daNhap + $row['SoLuongNhap'] > (float)$detail['SoLuongDat']) {
                    throw new Exception('Tổng số lượng nhập không được vượt quá số lượng đặt. Mặt hàng: ' . $row['MaHang']);
                }
                $row['ThanhTien'] = $row['SoLuongNhap'] * $row['GiaNhap'];
                $totalGoods += $row['ThanhTien'];
            }
            unset($row);
            $disc = round($totalGoods * $header['DiscPercent'] / 100, 2);
            $prom = round($totalGoods * $header['PromPercent'] / 100, 2);
            $com = round($totalGoods * $header['ComPercent'] / 100, 2);
            $tax = round($totalGoods * $header['TaxPercent'] / 100, 2);
            $total = max(0, $totalGoods - $disc - $prom - $com + $tax);
            $code = trim($header['MaPhieuNhap'] ?? '');
            if ($code === '') $code = $this->nextCodeWithConn($conn);
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM PhieuNhapKho WHERE MaPhieuNhap=?");
            $stmt->bind_param('s', $code);
            $stmt->execute();
            $exists = (int)$stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
            if ($exists > 0) $code = $this->nextCodeWithConn($conn);
            $today = date('Y-m-d');
            $status = 'Đã duyệt';
            $stmt = $conn->prepare("INSERT INTO PhieuNhapKho(MaPhieuNhap,SoDonNhap,LoaiDon,NgayLap,MaThuKho,DiscAmt,PromAmt,ComAmt,TaxAmt,TotalAmt,TrangThai) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param(
                'ssssiddddds',
                $code,
                $header['SoDonNhap'],
                $header['LoaiDon'],
                $today,
                $header['MaThuKho'],
                $disc,
                $prom,
                $com,
                $tax,
                $total,
                $status
            );
            $stmt->execute();
            $stmt->close();
            $insertDetail = $conn->prepare("INSERT INTO ChiTietPhieuNhap(MaPhieuNhap,MaHang,SoLuongNhap,GiaNhap,MaKho,SlProm,AmtProm,TaxAmt,ThanhTien) VALUES(?,?,?,?,?,?,?,?,?)");
            $updateStock = $conn->prepare("INSERT INTO TonKho(MaKho,MaHang,SoLuongTon) VALUES(?,?,?) ON DUPLICATE KEY UPDATE SoLuongTon=SoLuongTon+VALUES(SoLuongTon),CapNhatLuc=CURRENT_TIMESTAMP");
            foreach ($rows as $row) {
                $insertDetail->bind_param(
                    'ssddsdddd',
                    $code,
                    $row['MaHang'],
                    $row['SoLuongNhap'],
                    $row['GiaNhap'],
                    $row['MaKho'],
                    $row['SlProm'],
                    $row['AmtProm'],
                    $row['TaxAmtDong'],
                    $row['ThanhTien']
                );
                $insertDetail->execute();
                $updateStock->bind_param(
                    'ssd',
                    $row['MaKho'],
                    $row['MaHang'],
                    $row['SoLuongNhap']
                );
                $updateStock->execute();
            }
            $insertDetail->close();
            $updateStock->close();
            $stmt = $conn->prepare("SELECT COUNT(*) AS ConLai FROM ChiTietDonNhap ct WHERE ct.SoDonNhap=? AND ct.SoLuongDat>COALESCE((SELECT SUM(ctpn.SoLuongNhap) FROM PhieuNhapKho pn JOIN ChiTietPhieuNhap ctpn ON ctpn.MaPhieuNhap=pn.MaPhieuNhap WHERE pn.SoDonNhap=ct.SoDonNhap AND pn.TrangThai='Đã duyệt' AND ctpn.MaHang=ct.MaHang),0)");
            $stmt->bind_param('s', $header['SoDonNhap']);
            $stmt->execute();
            $conLai = (int)$stmt->get_result()->fetch_assoc()['ConLai'];
            $stmt->close();
            $newStatus = $conLai === 0 ? 'Đã nhận đủ' : 'Đã nhận một phần';
            $stmt = $conn->prepare("UPDATE DonNhapHang SET TrangThai=? WHERE SoDonNhap=?");
            $stmt->bind_param('ss', $newStatus, $header['SoDonNhap']);
            $stmt->execute();
            $stmt->close();
            $conn->commit();
            $p->dongketnoi($conn);
            return [
                'ok' => true,
                'message' => 'Lưu phiếu thành công: ' . $code,
                'code' => $code
            ];
        } catch (Throwable $e) {
            if ($conn) {
                try {
                    $conn->rollback();
                } catch (Throwable $ignore) {}
                $p->dongketnoi($conn);
            }
            return [
                'ok' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
?>