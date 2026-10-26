<?php
include_once __DIR__ . '/../Controller/cDieuChuyen.php';
$c=new clsCDieuChuyen();$error='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['uc_action']??'')==='save_transfer'){
 $res=$c->luuPhieu($_POST,(int)$_SESSION['user_id']);if($res['ok']){setFlash('success',$res['message']);header('Location: index.php?page=dieuchuyen');exit;}$error=$res['message'];
}
$warehouses=$c->layKho();$raw=$c->layHangVaTon();$recent=$c->layPhieuGanDay();$preview=$c->taoMaPreview();
$products=[];$stocks=[];foreach($raw as $r){if(!isset($products[$r['MaHang']]))$products[$r['MaHang']]=['MaHang'=>$r['MaHang'],'TenHang'=>$r['TenHang'],'GiaNhapMacDinh'=>(float)$r['GiaNhapMacDinh']];if($r['MaKho'])$stocks[$r['MaKho']][$r['MaHang']]=(float)$r['SoLuongTon'];}$products=array_values($products);
?>
<?php if($error):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?>

<form method="post" id="frmTransfer"><input type="hidden" name="uc_action" value="save_transfer">
<div class="card"><div class="section-head"><div><h2>1. Thông tin chung phiếu điều chuyển</h2><p>Mã phiếu và ngày điều chuyển được hệ thống quản lý.</p></div></div>
 <div class="form-grid">
  <div class="field"><label>Mã phiếu điều chuyển</label><input class="code-input" name="MaPhieuDC" value="<?=e($_POST['MaPhieuDC']??$preview)?>" readonly></div>
  <div class="field"><label>Ngày điều chuyển</label><input value="<?=e(date('Y-m-d'))?>" readonly></div>
  <div class="field"><label>Kho xuất *</label><select name="MaKhoXuat" id="dcSource" required><?php foreach($warehouses as $k):?><option value="<?=e($k['MaKho'])?>" <?=($_POST['MaKhoXuat']??'')===$k['MaKho']?'selected':''?>><?=e($k['MaKho'].' - '.$k['TenKho'])?></option><?php endforeach;?></select></div>
  <div class="field"><label>Kho nhập *</label><select name="MaKhoNhap" id="dcTarget" required><?php foreach($warehouses as $i=>$k):?><option value="<?=e($k['MaKho'])?>" <?=($_POST['MaKhoNhap']??($warehouses[1]['MaKho']??''))===$k['MaKho']?'selected':''?>><?=e($k['MaKho'].' - '.$k['TenKho'])?></option><?php endforeach;?></select></div>
  <div class="field" style="grid-column:1/-1"><label>Ghi chú</label><textarea name="GhiChu" placeholder="Ghi chú điều chuyển (nếu có)"><?=e($_POST['GhiChu']??'')?></textarea></div>
 </div>
</div>
<div class="card"><div class="section-head"><div><h2>2. Chi tiết hàng hóa xuất chuyển</h2><p>Số lượng phải là số nguyên dương và không vượt tồn kho.</p></div><button type="button" class="btn btn-primary" id="dcAdd">+ Thêm dòng</button></div>
 <div class="table-wrap"><table><thead><tr><th>STT</th><th>Mã hàng</th><th>Tồn kho xuất</th><th>Số lượng chuyển</th><th>Đơn giá</th><th class="text-right">Thành tiền</th><th></th></tr></thead><tbody id="dcBody"></tbody></table></div>
 <div class="finance-box"><div class="finance-total"><span>TỔNG GIÁ TRỊ PHIẾU CHUYỂN</span><span id="dcTotal">0 ₫</span></div></div>
 <div class="actions"><button type="button" class="btn btn-light" onclick="if(confirmAction('Hủy thao tác lập phiếu?')) location.reload()">Hủy</button><button class="btn btn-primary">Xác nhận lưu phiếu</button></div>
</div></form>
<div class="card"><div class="section-head"><div><h2>Phiếu điều chuyển gần đây</h2></div></div><div class="table-wrap"><table><thead><tr><th>Mã phiếu</th><th>Ngày</th><th>Kho xuất</th><th>Kho nhập</th><th class="text-right">Tổng giá trị</th><th>Trạng thái</th></tr></thead><tbody>
<?php if(!$recent):?><tr><td colspan="6" class="empty-state">Chưa có phiếu điều chuyển.</td></tr><?php endif;?>
<?php foreach($recent as $r):?><tr><td class="text-blue"><?=e($r['MaPhieuDC'])?></td><td><?=e($r['NgayDieuChuyen'])?></td><td><?=e($r['KhoXuat'])?></td><td><?=e($r['KhoNhap'])?></td><td class="text-right"><b><?=money($r['TongGiaTri'])?></b></td><td><span class="badge badge-green"><?=e($r['TrangThai'])?></span></td></tr><?php endforeach;?>
</tbody></table></div></div>
<script>
const DC_PRODUCTS=<?=json_encode($products,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>,DC_STOCKS=<?=json_encode($stocks,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
const DC_POST=<?=json_encode(array_map(function($i){return ['MaHang'=>$_POST['MaHang'][$i]??'','SoLuong'=>$_POST['SoLuong'][$i]??1,'DonGia'=>$_POST['DonGia'][$i]??0];},array_keys($_POST['MaHang']??[])),JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)?>;
let dcRows=DC_POST.length?DC_POST.map(x=>({...x})):[];
function dcStock(ma){const k=document.getElementById('dcSource')?.value;return Number(DC_STOCKS[k]?.[ma]||0)}
function firstStockProduct(){return DC_PRODUCTS.find(p=>dcStock(p.MaHang)>0)||DC_PRODUCTS[0]}
function renderDc(){const body=document.getElementById('dcBody');body.innerHTML='';dcRows.forEach((r,i)=>{const opts=DC_PRODUCTS.map(p=>`<option value="${escapeHtml(p.MaHang)}" ${p.MaHang===r.MaHang?'selected':''}>${escapeHtml(p.MaHang+' - '+p.TenHang)}</option>`).join(''),stock=dcStock(r.MaHang),line=Number(r.SoLuong||0)*Number(r.DonGia||0);body.insertAdjacentHTML('beforeend',`<tr><td class="text-center">${i+1}</td><td><select class="table-input select-wide" name="MaHang[]" onchange="dcField(${i},'MaHang',this.value,true)">${opts}</select></td><td class="text-center"><span class="badge ${stock>0?'badge-blue':'badge-red'}">${stock}</span></td><td><input class="table-input" type="number" min="1" step="1" max="${stock}" name="SoLuong[]" value="${escapeHtml(r.SoLuong)}" oninput="dcField(${i},'SoLuong',this.value)"></td><td><input class="table-input" type="number" min="0" name="DonGia[]" value="${escapeHtml(r.DonGia)}" oninput="dcField(${i},'DonGia',this.value)"></td><td class="text-right text-blue">${formatMoney(line)}</td><td><button type="button" class="btn btn-danger btn-sm" onclick="dcDelete(${i})">Xóa</button></td></tr>`)});document.getElementById('dcTotal').textContent=formatMoney(dcRows.reduce((s,r)=>s+Number(r.SoLuong||0)*Number(r.DonGia||0),0))}
function dcField(i,f,v,productChanged=false){dcRows[i][f]=v;if(productChanged){const p=DC_PRODUCTS.find(x=>x.MaHang===v);if(p){dcRows[i].DonGia=p.GiaNhapMacDinh;dcRows[i].SoLuong=Math.min(1,dcStock(v))||1}}renderDc()}
function dcDelete(i){dcRows.splice(i,1);renderDc()}
function addDc(){const p=firstStockProduct();if(!p)return;dcRows.push({MaHang:p.MaHang,SoLuong:1,DonGia:p.GiaNhapMacDinh});renderDc()}
document.getElementById('dcAdd')?.addEventListener('click',addDc);document.getElementById('dcSource')?.addEventListener('change',()=>{dcRows=[];addDc()});if(!dcRows.length)addDc();else renderDc();
</script>
