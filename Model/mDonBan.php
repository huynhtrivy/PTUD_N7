<?php
include_once __DIR__ . '/mKetnoi.php';

class clsMDonBan {
    private function nextCodeWithConn($conn) {
        $rs = $conn->query("SELECT COALESCE(MAX(CAST(SUBSTRING(SoDonHang,3) AS UNSIGNED)),0)+1 n FROM DonBanHang WHERE SoDonHang REGEXP '^DH[0-9]+$'");
        return 'DH' . str_pad((string)((int)$rs->fetch_assoc()['n']), 6, '0', STR_PAD_LEFT);
    }
    function nextCode() { $p=new clsKetnoi(); $c=$p->moketnoi(); $x=$this->nextCodeWithConn($c); $p->dongketnoi($c); return $x; }

    function getKhachHang() {
        $p=new clsKetnoi(); $c=$p->moketnoi();
        $rows=$c->query("SELECT * FROM KhachHang WHERE TrangThai='Hoạt động' ORDER BY TenKH")->fetch_all(MYSQLI_ASSOC);
        $p->dongketnoi($c); return $rows;
    }
    function getHangHoa() {
        $p=new clsKetnoi(); $c=$p->moketnoi();
        $rows=$c->query("SELECT MaHang,TenHang,DonViTinh,DonGiaBan,ThueSuat FROM HangHoa WHERE TrangThai='Hoạt động' ORDER BY TenHang")->fetch_all(MYSQLI_ASSOC);
        $p->dongketnoi($c); return $rows;
    }
    function getDonGanDay() {
        $p=new clsKetnoi(); $c=$p->moketnoi();
        $rows=$c->query("SELECT d.SoDonHang,d.NgayLap,d.NgayQuaHan,k.TenKH,t.HoTen NhanVien,d.TongTien,d.TrangThai
                         FROM DonBanHang d JOIN KhachHang k ON k.MaKH=d.MaKH JOIN TaiKhoan t ON t.MaTaiKhoan=d.MaNhanVien
                         ORDER BY d.NgayLap DESC,d.SoDonHang DESC LIMIT 12")->fetch_all(MYSQLI_ASSOC);
        $p->dongketnoi($c); return $rows;
    }

    function saveDonBan($header, $rows) {
        $p=new clsKetnoi(); $conn=null;
        try {
            $conn=$p->moketnoi(); $conn->begin_transaction();
            $stmt=$conn->prepare("SELECT MaKH,HanNo,HanMucNo,NoTrongHan,NoQuaHan FROM KhachHang WHERE MaKH=? AND TrangThai='Hoạt động' FOR UPDATE");
            $stmt->bind_param('s',$header['MaKH']); $stmt->execute(); $kh=$stmt->get_result()->fetch_assoc(); $stmt->close();
            if(!$kh) throw new Exception('Không tìm thấy hoặc không thể lấy thông tin khách hàng đã chọn.');

            $seen=[]; $prepared=[]; $tongHang=0; $tongCK=0; $tongThue=0; $tong=0;
            $q=$conn->prepare("SELECT MaHang,TenHang,ThueSuat FROM HangHoa WHERE MaHang=? AND TrangThai='Hoạt động'");
            foreach($rows as $r){
                if($r['SoLuong']<=0 || $r['DonGia']<=0) throw new Exception('Số lượng hoặc đơn giá phải lớn hơn 0.');
                if($r['ChietKhau']<0 || $r['ChietKhau']>100 || $r['ThueSuat']<0 || $r['ThueSuat']>100) throw new Exception('Chiết khấu và thuế phải từ 0 đến 100%.');
                if(isset($seen[$r['MaHang']])) throw new Exception('Mặt hàng '.$r['MaHang'].' bị thêm trùng.');
                $seen[$r['MaHang']]=true;
                $q->bind_param('s',$r['MaHang']); $q->execute(); $hh=$q->get_result()->fetch_assoc();
                if(!$hh) throw new Exception('Hàng hóa '.$r['MaHang'].' không còn hợp lệ.');
                $base=$r['SoLuong']*$r['DonGia'];
                $ck=round($base*$r['ChietKhau']/100,2);
                $taxable=$base-$ck;
                $tax=round($taxable*$r['ThueSuat']/100,2);
                $line=$taxable+$tax;
                $r['TienChietKhau']=$ck; $r['TienThue']=$tax; $r['ThanhTien']=$line;
                $prepared[]=$r; $tongHang+=$base; $tongCK+=$ck; $tongThue+=$tax; $tong+=$line;
            }
            $q->close();
            if(!$prepared) throw new Exception('Đơn hàng phải có ít nhất một mặt hàng.');

            $code=$header['SoDonHang'] ?: $this->nextCodeWithConn($conn);
            $stmt=$conn->prepare("SELECT COUNT(*) c FROM DonBanHang WHERE SoDonHang=?"); $stmt->bind_param('s',$code); $stmt->execute();
            if((int)$stmt->get_result()->fetch_assoc()['c']>0) $code=$this->nextCodeWithConn($conn); $stmt->close();
            $ngay=date('Y-m-d'); $hanNo=(int)$kh['HanNo']; $quaHan=date('Y-m-d',strtotime($ngay.' +'.$hanNo.' days')); $status='Chờ thanh toán';
            $stmt=$conn->prepare("INSERT INTO DonBanHang(SoDonHang,MaKH,MaNhanVien,NgayLap,NgayQuaHan,TongTienHang,TongChietKhau,TongTienThue,TongTien,TrangThai)
                                  VALUES(?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('ssissdddds',$code,$header['MaKH'],$header['MaNhanVien'],$ngay,$quaHan,$tongHang,$tongCK,$tongThue,$tong,$status);
            $stmt->execute(); $stmt->close();

            $ins=$conn->prepare("INSERT INTO ChiTietDonBan(SoDonHang,MaHang,SoLuong,DonGia,ChietKhauPhanTram,TienChietKhau,ThueSuat,TienThue,ThanhTien)
                                 VALUES(?,?,?,?,?,?,?,?,?)");
            foreach($prepared as $r){
                $ins->bind_param('ssddddddd',$code,$r['MaHang'],$r['SoLuong'],$r['DonGia'],$r['ChietKhau'],$r['TienChietKhau'],$r['ThueSuat'],$r['TienThue'],$r['ThanhTien']);
                $ins->execute();
            }
            $ins->close();
            $stmt=$conn->prepare("UPDATE KhachHang SET NoTrongHan=NoTrongHan+? WHERE MaKH=?"); $stmt->bind_param('ds',$tong,$header['MaKH']); $stmt->execute(); $stmt->close();
            $conn->commit(); $p->dongketnoi($conn);
            return ['ok'=>true,'message'=>'Thêm đơn bán hàng thành công: '.$code,'code'=>$code];
        }catch(Throwable $e){
            if($conn){try{$conn->rollback();}catch(Throwable $ignore){} $p->dongketnoi($conn);} return ['ok'=>false,'message'=>$e->getMessage()];
        }
    }
}
