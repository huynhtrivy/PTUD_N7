<?php
include_once __DIR__ . '/../Controller/cDanhMuc.php';
$c=new clsCDanhMuc();$error='';
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['uc_action']??'')==='add_category'){
 $res=$c->themDanhMuc($_POST);if($res['ok']){setFlash('success',$res['message']);header('Location: index.php?page=danhmuc');exit;}$error=$res['message'];
}
$rows=$c->layDanhSach();
?>
<?php if($error):?><div class="alert alert-error"><?=e($error)?></div><?php endif;?>
<div class="hint"><b>UC 3:</b> Kế toán quản lý danh mục hàng hóa. Khi thêm mới, hệ thống kiểm tra dữ liệu bắt buộc và không cho trùng mã hoặc tên danh mục.</div>
<div class="stat-grid">
 <div class="stat-card"><span>Tổng danh mục</span><strong><?=count($rows)?></strong></div>
 <div class="stat-card"><span>Đang hoạt động</span><strong><?=count(array_filter($rows,fn($x)=>$x['TrangThai']==='Hoạt động'))?></strong></div>
 <div class="stat-card"><span>Ngừng hoạt động</span><strong><?=count(array_filter($rows,fn($x)=>$x['TrangThai']!=='Hoạt động'))?></strong></div>
 <div class="stat-card"><span>Tổng mặt hàng</span><strong><?=array_sum(array_map(fn($x)=>(int)$x['SoMatHang'],$rows))?></strong></div>
</div>
<div class="card">
 <div class="section-head"><div><h2>Danh mục hàng hóa</h2><p>Danh sách hiện có trong CSDL.</p></div><button class="btn btn-primary" type="button" onclick="document.getElementById('catModal').classList.add('show')">+ Thêm danh mục</button></div>
 <div class="table-wrap"><table><thead><tr><th>Mã danh mục</th><th>Tên/Chủng loại</th><th>Mô tả</th><th>Số mặt hàng</th><th>Trạng thái</th></tr></thead><tbody>
 <?php foreach($rows as $r):?><tr><td class="text-blue"><?=e($r['MaDanhMuc'])?></td><td><b><?=e($r['ChungLoai'])?></b></td><td><?=e($r['MoTa'])?></td><td><?=e($r['SoMatHang'])?></td><td><span class="badge <?=$r['TrangThai']==='Hoạt động'?'badge-green':'badge-gray'?>"><?=e($r['TrangThai'])?></span></td></tr><?php endforeach;?>
 </tbody></table></div>
</div>
<div class="modal-backdrop <?=$error?'show':''?>" id="catModal"><div class="modal">
 <div class="modal-head"><div><h2>Thêm danh mục hàng hóa</h2><p class="text-muted">Các trường có dấu * là bắt buộc.</p></div><button class="modal-close" type="button" onclick="document.getElementById('catModal').classList.remove('show')">×</button></div>
 <form method="post" class="stack-form"><input type="hidden" name="uc_action" value="add_category">
   <div class="form-grid"><div class="field"><label>Mã danh mục *</label><input name="MaDanhMuc" placeholder="VD: DM006" value="<?=e($_POST['MaDanhMuc']??'')?>" required></div><div class="field"><label>Tên danh mục *</label><input name="ChungLoai" placeholder="VD: Sữa cho trẻ 0-6 tháng" value="<?=e($_POST['ChungLoai']??'')?>" required></div></div>
   <div class="field"><label>Mô tả</label><textarea name="MoTa" placeholder="Mô tả danh mục..."><?=e($_POST['MoTa']??'')?></textarea></div>
   <div class="field"><label>Trạng thái</label><select name="TrangThai"><option>Hoạt động</option><option>Ngừng hoạt động</option></select></div>
   <div class="actions"><button type="button" class="btn btn-light" onclick="document.getElementById('catModal').classList.remove('show')">Hủy</button><button class="btn btn-primary">Lưu danh mục</button></div>
 </form>
</div></div>
