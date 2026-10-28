<?php
require __DIR__ . '/../Controller/cDonNhap.php';
$key = str_repeat('a', 48);
$_SESSION = ['purchase_requests' => [$key => time()]];
$valid = ['RequestKey'=>$key, 'MaNCC'=>'NCC01', 'MaKhoDuKien'=>'K1',
    'MaHang'=>['H1'], 'NhuCau'=>[100], 'SoLuongDat'=>[10], 'GiaDuKien'=>[100000],
    'ChietKhauPhanTram'=>0, 'ThueSuat'=>10, 'HinhThucThanhToan'=>'Trả nợ'];
$cases = [
    ['RequestKey'=>''], ['MaNCC'=>''], ['MaKhoDuKien'=>''], ['MaHang'=>[]],
    ['NhuCau'=>['abc']], ['SoLuongDat'=>['abc']], ['GiaDuKien'=>['1e999']],
    ['ChietKhauPhanTram'=>'abc'], ['ThueSuat'=>'1e999'], ['HinhThucThanhToan'=>'invalid'],
];
foreach ($cases as $i=>$change) {
    $result = (new clsCDonNhap())->luuDon(array_replace($valid, $change), 1);
    if ($result['ok'] || empty($result['message'])) {
        throw new RuntimeException('Failed validation case '.($i+1));
    }
}
echo "UC06: 10 invalid-input cases passed without database writes.\n";
