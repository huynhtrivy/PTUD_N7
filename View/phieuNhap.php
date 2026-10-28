<?php
include_once __DIR__ . '/../Controller/cPhieuNhap.php';
$c = new clsCPhieuNhap();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['uc_action'] ?? '') === 'save_phieu_nhap') {
    $res = $c->luuPhieuNhap($_POST, (int)($_SESSION['user_id'] ?? 0));
    if ($res['ok']) {
        setFlash('success', $res['message']);
        header('Location: index.php?page=phieunhap');
        exit;
    }
    $error = $res['message'];
}
$orders = $c->layDuLieuDonNhapChoNhan();
$allReceipts = $c->layDanhSachPhieuNhap();
$preview = $c->taoMaPhieuPreview();
$recentReceipts = $allReceipts;
usort($recentReceipts, function ($a, $b) {
    $ngayA = strtotime($a['NgayLap'] ?? '1970-01-01');
    $ngayB = strtotime($b['NgayLap'] ?? '1970-01-01');
    if ($ngayA == $ngayB) {
        return strcmp($b['MaPhieuNhap'] ?? '', $a['MaPhieuNhap'] ?? '');
    }
    return $ngayB <=> $ngayA;
});
$selectedOrder = $_POST['SoDonNhap'] ?? ($orders[0]['SoDonNhap'] ?? '');
$postRows = [];
foreach (array_keys($_POST['MaHang'] ?? []) as $i) {
    $postRows[] = [
        'MaHang' => $_POST['MaHang'][$i] ?? '',
        'SoLuongNhap' => $_POST['SoLuongNhap'][$i] ?? 0,
        'GiaNhap' => $_POST['GiaNhap'][$i] ?? 0,
        'MaKho' => $_POST['MaKho'][$i] ?? '',
        'SlProm' => $_POST['SlProm'][$i] ?? 0
    ];
}
?>
<?php if ($error): ?>
<div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>
<div class="phieu-nhap-page">
<div class="pn-tabs">
    <button type="button" class="pn-tab active" onclick="switchPnTab('lap',this)">+ Lập phiếu nhập</button>
    <button type="button" class="pn-tab" onclick="switchPnTab('danhsach',this)">☷ Danh sách phiếu nhập</button>
</div>
<style>
.pn-tabs{display:flex;gap:10px;margin-bottom:20px;border-bottom:1px solid #e2e8f0}
.pn-tab{padding:12px 20px;border:0;border-radius:10px 10px 0 0;background:#f1f5f9;color:#475569;font-size:14px;font-weight:700;cursor:pointer}
.pn-tab:hover{background:#e2e8f0}
.pn-tab.active{background:#2563eb;color:#fff}
.pn-tab-content{width:100%}
</style>
<div id="pn-tab-lap" class="pn-tab-content">
<form method="post" id="frmPhieuNhap">
<input type="hidden" name="uc_action" value="save_phieu_nhap">
<input type="hidden" name="LoaiDon" id="pnLoaiDonHidden">
<div class="card">
    <div class="section-head">
        <div>
            <h2>1. Thông tin chung phiếu nhập</h2>
            <p>Chọn đơn đặt nhập hàng để lập phiếu nhập kho.</p>
        </div>
    </div>
    <?php if (!$orders): ?>
    <div class="alert alert-info">Hiện không có đơn đặt nhập hàng đang chờ nhận.</div>
    <?php endif; ?>
    <div class="form-grid">
        <div class="field">
            <label>Mã phiếu nhập</label>
            <input type="text" name="MaPhieuNhap" id="pnCode" class="code-input" value="<?= e($_POST['MaPhieuNhap'] ?? $preview) ?>" readonly>
        </div>
        <div class="field">
            <label>Số đơn hàng tham chiếu</label>
            <select name="SoDonNhap" id="pnOrder" required <?= !$orders ? 'disabled' : '' ?>>
                <?php foreach ($orders as $o): ?>
                <option value="<?= e($o['SoDonNhap']) ?>" <?= $selectedOrder === $o['SoDonNhap'] ? 'selected' : '' ?>>
                    <?= e($o['SoDonNhap'] . ' - ' . $o['TenNCC']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label>Loại đơn</label>
            <select id="pnLoaiDon" disabled>
                <option value="NP">NP (Bán nợ)</option>
                <option value="NM">NM (KH trả)</option>
                <option value="ND">ND (Ký gửi)</option>
                <option value="IN">IN (Tiền ngay)</option>
                <option value="CM">CM (Trả hàng IN)</option>
            </select>
        </div>
        <div class="field">
            <label>Ngày lập</label>
            <input type="date" value="<?= date('Y-m-d') ?>" readonly>
        </div>
        <div class="field">
            <label>Nhà cung cấp</label>
            <input type="text" id="pnNcc" readonly>
        </div>
        <div class="field">
            <label>Kho dự kiến nhận</label>
            <input type="text" id="pnKho" readonly>
        </div>
    </div>
</div>
<div class="card">
    <div class="section-head">
        <div>
            <h2>2. Chi tiết hàng hóa</h2>
            <p>Nhập số lượng hàng thực tế nhận.</p>
        </div>
        <button type="button" class="btn btn-primary" id="pnAdd">+ Thêm mặt hàng khác</button>
    </div>
    <div class="table-wrap pn-table-wrap">
        <table class="pn-detail-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã hàng</th>
                    <th>Số lượng</th>
                    <th>Giá nhập</th>
                    <th>Mã kho</th>
                    <th>SL KM</th>
                    <th>Thành tiền</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody id="pnBody"></tbody>
        </table>
    </div>
    <div class="section-head" style="margin-top:25px;">
        <h2>3. Tổng hợp giá trị hóa đơn</h2>
    </div>
    <div class="finance-box">
        <div class="pn-finance-row">
            <span>Tiền hàng:</span>
            <strong id="pnGoodsTotal">0 ₫</strong>
        </div>
        <div class="pn-finance-row">
            <span>Chiết khấu:</span>
            <div class="pn-percent-money">
                <input type="number" name="DiscPercent" id="pnDiscPercent" min="0" max="100" step="1" value="<?= e($_POST['DiscPercent'] ?? 0) ?>" oninput="percentToMoney('Disc')">
                <span>% =</span>
                <input type="number" id="pnDiscAmt" min="0" value="0" oninput="moneyToPercent('Disc')">
            </div>
        </div>
        <div class="pn-finance-row">
            <span>Thuế:</span>
            <div class="pn-percent-money">
                <input type="number" name="TaxPercent" id="pnTaxPercent" min="0" max="100" step="1" value="<?= e($_POST['TaxPercent'] ?? 0) ?>" oninput="percentToMoney('Tax')">
                <span>% =</span>
                <input type="number" id="pnTaxAmt" min="0" value="0" oninput="moneyToPercent('Tax')">
            </div>
        </div>
        <div class="finance-total">
            <span>TỔNG THANH TOÁN:</span>
            <span id="pnTotal">0 ₫</span>
        </div>
    </div>
    <div class="actions" style="display:flex;justify-content:flex-end;gap:10px;">
        <button type="button" class="btn btn-light" onclick="resetForm()">Làm mới</button>
        <button type="submit" class="btn btn-primary" <?= !$orders ? 'disabled' : '' ?>>Xác nhận lưu phiếu</button>
    </div>
</div>
</form>
</div>
<div id="pn-tab-danhsach" class="pn-tab-content" style="display:none">
<div class="card">
    <div class="section-head">
        <div>
            <h2>Danh sách phiếu nhập</h2>
            <p>Các phiếu nhập kho đã lập.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mã phiếu</th>
                    <th>Đơn hàng</th>
                    <th>Loại đơn</th>
                    <th>Ngày lập</th>
                    <th>Nhà cung cấp</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$recentReceipts): ?>
                <tr>
                    <td colspan="8" class="empty-state">Chưa có phiếu nhập kho.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($recentReceipts as $p): ?>
                <tr>
                    <td><strong class="text-blue"><?= e($p['MaPhieuNhap']) ?></strong></td>
                    <td><?= e($p['SoDonNhap']) ?></td>
                    <td><?= e($p['LoaiDon'] ?? '') ?></td>
                    <td><?= !empty($p['NgayLap']) ? date('d/m/Y', strtotime($p['NgayLap'])) : '' ?></td>
                    <td><?= e($p['TenNCC'] ?? '') ?></td>
                    <td><?= number_format((float)($p['TotalAmt'] ?? 0), 0, ',', '.') ?> ₫</td>
                    <td><span class="badge badge-green"><?= e($p['TrangThai'] ?? '') ?></span></td>
                    <td>
                        <button type="button" class="btn btn-light btn-sm" onclick="openPrint('<?= e($p['MaPhieuNhap']) ?>')">In</button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
<div class="modal-backdrop" id="pnPrintModal">
    <div class="modal modal-wide">
        <div class="print-preview" id="pnPrintContent">
            <div class="print-header">
                <div><b>THÀNH SHOP</b><br>Hệ thống Quản lý Kho</div>
                <div><svg id="pnBarcode"></svg></div>
                <div><b>Phiếu nhập kho</b></div>
            </div>
            <div class="print-title">PHIẾU NHẬP KHO</div>
            <div class="print-info-grid">
                <div><b>Mã phiếu:</b> <span id="printMaPhieu"></span></div>
                <div><b>Đơn hàng:</b> <span id="printDonHang"></span></div>
                <div><b>Loại đơn:</b> <span id="printLoaiDon"></span></div>
                <div><b>Ngày lập:</b> <span id="printNgayLap"></span></div>
                <div><b>Nhà cung cấp:</b> <span id="printNcc"></span></div>
                <div><b>Kho:</b> <span id="printKho"></span></div>
            </div>
            <table class="print-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã hàng</th>
                        <th>Tên hàng</th>
                        <th>SL nhập</th>
                        <th>SL tặng</th>
                        <th>Giá nhập</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody id="printDetailBody"></tbody>
            </table>
            <div class="print-footer-sum">
                <div>Tổng thanh toán: <strong id="printTotal"></strong></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" onclick="closePrint()">Đóng</button>
            <button type="button" class="btn btn-primary" onclick="window.print()">In phiếu</button>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
function switchPnTab(tab, btn) {
    document.querySelectorAll('.pn-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.pn-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('pn-tab-' + tab).style.display = 'block';
    btn.classList.add('active');
}
const ORDERS = <?= json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const RECEIPTS = <?= json_encode($allReceipts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const POST_ROWS = <?= json_encode($postRows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const HAS_POST = <?= $_SERVER['REQUEST_METHOD'] === 'POST' ? 'true' : 'false' ?>;
const PREVIEW = <?= json_encode($preview) ?>;
let rows = [];
function money(value) {
    return Number(value || 0).toLocaleString('vi-VN') + ' ₫';
}
function getOrder() {
    const maDon = document.getElementById('pnOrder')?.value;
    return ORDERS.find(x => x.SoDonNhap === maDon) || null;
}
function getProduct(maHang) {
    const order = getOrder();
    if (!order) return null;
    return order.chiTiet.find(x => x.MaHang === maHang) || null;
}
function loadOrder() {
    const order = getOrder();
    if (!order) {
        rows = [];
        renderRows();
        return;
    }
    document.getElementById('pnLoaiDon').value = order.LoaiDon || 'NP';
    document.getElementById('pnLoaiDonHidden').value = order.LoaiDon || 'NP';
    document.getElementById('pnNcc').value = order.TenNCC || '';
    document.getElementById('pnKho').value = `${order.MaKhoDuKien || ''} - ${order.TenKho || ''}`;
    document.getElementById('pnDiscPercent').value = HAS_POST
        ? Number(<?= json_encode($_POST['DiscPercent'] ?? null) ?> ?? order.ChietKhauPhanTram ?? 0)
        : Number(order.ChietKhauPhanTram || 0);
    document.getElementById('pnTaxPercent').value = HAS_POST
        ? Number(<?= json_encode($_POST['TaxPercent'] ?? null) ?> ?? order.ThueSuat ?? 0)
        : Number(order.ThueSuat || 0);
    if (HAS_POST && POST_ROWS.length && order.SoDonNhap === <?= json_encode($selectedOrder) ?>) {
        rows = POST_ROWS.map(x => ({...x}));
    } else {
        rows = order.chiTiet.map(x => ({
            MaHang: x.MaHang,
            SoLuongNhap: Math.floor(Number(x.ConLai || 0)),
            GiaNhap: x.GiaDuKien,
            MaKho: order.MaKhoDuKien,
            SlProm: 0
        }));
    }
    renderRows();
    refreshFinance();
}
function renderRows() {
    const tbody = document.getElementById('pnBody');
    const order = getOrder();
    tbody.innerHTML = '';
    if (!order || !rows.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="empty-state">Chưa có mặt hàng.</td></tr>';
        calcTotal();
        return;
    }
    rows.forEach((row, index) => {
        const product = getProduct(row.MaHang);
        const conLai = Math.floor(Number(product?.ConLai || 0));
        const options = order.chiTiet.map(x =>
            `<option value="${x.MaHang}" ${x.MaHang === row.MaHang ? 'selected' : ''}>${x.MaHang} - ${x.TenHang}</option>`
        ).join('');
        const thanhTien = Number(row.SoLuongNhap || 0) * Number(row.GiaNhap || 0);
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
                <td>${index + 1}</td>
                <td>
                    <select class="table-input" name="MaHang[]" onchange="changeProduct(${index},this.value)">
                        ${options}
                    </select>
                </td>
                <td>
                    <div class="pn-qty-wrap">
                        <input class="table-input" type="number" name="SoLuongNhap[]" min="1" max="${conLai}" step="1" value="${Math.floor(Number(row.SoLuongNhap || 0))}" oninput="updateRow(${index},'SoLuongNhap',this.value,this)">
                        <span class="pn-remaining">Còn: ${conLai}</span>
                    </div>
                </td>
                <td>
                    <input class="table-input" type="number" name="GiaNhap[]" min="0" step="10000" value="${row.GiaNhap}" oninput="updateRow(${index},'GiaNhap',this.value,this)">
                </td>
                <td>
                    <input class="table-input" value="${order.MaKhoDuKien}" readonly>
                    <input type="hidden" name="MaKho[]" value="${order.MaKhoDuKien}">
                </td>
                <td>
                    <input class="table-input" type="number" name="SlProm[]" min="0" step="1" value="${Math.floor(Number(row.SlProm || 0))}" oninput="updateRow(${index},'SlProm',this.value,this)">
                </td>
                <td class="line-total">${money(thanhTien)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${index})">Xóa</button>
                </td>
            </tr>
        `);
    });
    calcTotal();
}
function updateRow(index, field, value, input) {
    if (field === 'SoLuongNhap' || field === 'SlProm') {
        value = Math.floor(Number(value || 0));
        input.value = value;
    }
    rows[index][field] = value;
    const tr = input.closest('tr');
    const thanhTien = Number(rows[index].SoLuongNhap || 0) * Number(rows[index].GiaNhap || 0);
    const cell = tr.querySelector('.line-total');
    if (cell) cell.textContent = money(thanhTien);
    refreshFinance();
}
function changeProduct(index, maHang) {
    const product = getProduct(maHang);
    const order = getOrder();
    if (!product || !order) return;
    const trung = rows.some((x, i) => i !== index && x.MaHang === maHang);
    if (trung) {
        alert('Mặt hàng này đã có trong phiếu.');
        renderRows();
        return;
    }
    rows[index].MaHang = maHang;
    rows[index].SoLuongNhap = Math.floor(Number(product.ConLai || 0));
    rows[index].GiaNhap = product.GiaDuKien;
    rows[index].MaKho = order.MaKhoDuKien;
    renderRows();
    refreshFinance();
}
function addRow() {
    const order = getOrder();
    if (!order) return;
    const daCo = rows.map(x => x.MaHang);
    const item = order.chiTiet.find(x => !daCo.includes(x.MaHang));
    if (!item) {
        alert('Tất cả mặt hàng đã được thêm.');
        return;
    }
    rows.push({
        MaHang: item.MaHang,
        SoLuongNhap: 1,
        GiaNhap: item.GiaDuKien,
        MaKho: order.MaKhoDuKien,
        SlProm: 0
    });
    renderRows();
    refreshFinance();
}
function deleteRow(index) {
    rows.splice(index, 1);
    renderRows();
    refreshFinance();
}
function goodsTotal() {
    return rows.reduce((tong, row) =>
        tong + Number(row.SoLuongNhap || 0) * Number(row.GiaNhap || 0), 0
    );
}
function baseForPercent(type) {
    const total = goodsTotal();
    if (type === 'Tax') {
        const disc = Number(document.getElementById('pnDiscAmt').value || 0);
        return Math.max(0, total - disc);
    }
    return total;
}
function percentToMoney(type) {
    const percentInput = document.getElementById(`pn${type}Percent`);
    const percent = Math.min(100, Math.max(0, Number(percentInput.value || 0)));
    percentInput.value = percent;
    document.getElementById(`pn${type}Amt`).value = Math.round(baseForPercent(type) * percent / 100);
    if (type === 'Disc') percentToMoney('Tax');
    calcTotal();
}
function moneyToPercent(type) {
    const amountInput = document.getElementById(`pn${type}Amt`);
    const base = baseForPercent(type);
    const amount = Math.min(base, Math.max(0, Number(amountInput.value || 0)));
    amountInput.value = amount;
    document.getElementById(`pn${type}Percent`).value = base > 0
        ? (amount / base * 100).toFixed(2)
        : 0;
    if (type === 'Disc') percentToMoney('Tax');
    calcTotal();
}
function refreshFinance() {
    percentToMoney('Disc');
}
function calcTotal() {
    const goods = goodsTotal();
    const disc = Number(document.getElementById('pnDiscAmt').value || 0);
    const tax = Number(document.getElementById('pnTaxAmt').value || 0);
    document.getElementById('pnGoodsTotal').textContent = money(goods);
    document.getElementById('pnTotal').textContent = money(Math.max(0, goods - disc + tax));
}
function resetForm() {
    const order = getOrder();
    if (!order) return;
    if (!confirm('Làm mới dữ liệu?')) return;
    document.getElementById('pnCode').value = PREVIEW;
    rows = order.chiTiet.map(x => ({
        MaHang: x.MaHang,
        SoLuongNhap: Math.floor(Number(x.ConLai || 0)),
        GiaNhap: x.GiaDuKien,
        MaKho: order.MaKhoDuKien,
        SlProm: 0
    }));
    renderRows();
    refreshFinance();
}
function openPrint(maPhieu) {
    const p = RECEIPTS.find(x => x.MaPhieuNhap === maPhieu);
    if (!p) {
        alert('Không tìm thấy phiếu.');
        return;
    }
    document.getElementById('printMaPhieu').textContent = p.MaPhieuNhap || '';
    document.getElementById('printDonHang').textContent = p.SoDonNhap || '';
    document.getElementById('printLoaiDon').textContent = p.LoaiDon || '';
    document.getElementById('printNgayLap').textContent = p.NgayLap || '';
    document.getElementById('printNcc').textContent = p.TenNCC || '';
    document.getElementById('printKho').textContent = `${p.MaKho || ''} - ${p.TenKho || ''}`;
    document.getElementById('printTotal').textContent = money(p.TotalAmt);
    document.getElementById('printDetailBody').innerHTML = (p.chiTiet || []).map((ct, i) => `
        <tr>
            <td>${i + 1}</td>
            <td>${ct.MaHang}</td>
            <td>${ct.TenHang || ''}</td>
            <td>${ct.SoLuongNhap}</td>
            <td>${ct.SlProm || 0}</td>
            <td>${money(ct.GiaNhap)}</td>
            <td>${money(ct.ThanhTien || Number(ct.SoLuongNhap) * Number(ct.GiaNhap))}</td>
        </tr>
    `).join('');
    if (window.JsBarcode) {
        JsBarcode('#pnBarcode', p.MaPhieuNhap, {
            format: 'CODE128',
            height: 40,
            displayValue: true
        });
    }
    document.getElementById('pnPrintModal').classList.add('show');
}
function closePrint() {
    document.getElementById('pnPrintModal').classList.remove('show');
}
function validateForm(event) {
    for (const type of ['Disc', 'Tax']) {
        const percent = Number(document.getElementById(`pn${type}Percent`).value);
        if (!Number.isFinite(percent) || percent < 0 || percent > 100) {
            event.preventDefault();
            alert('Chiết khấu và thuế phải nằm trong khoảng 0 đến 100%.');
            return;
        }
    }
    if (!rows.length) {
        event.preventDefault();
        alert('Phiếu nhập chưa có mặt hàng.');
        return;
    }
    for (const row of rows) {
        const product = getProduct(row.MaHang);
        const soLuong = Number(row.SoLuongNhap || 0);
        const slKM = Number(row.SlProm || 0);
        const gia = Number(row.GiaNhap || 0);
        if (!Number.isInteger(soLuong) || soLuong <= 0) {
            event.preventDefault();
            alert('Số lượng nhập phải là số nguyên lớn hơn 0.');
            return;
        }
        if (!Number.isInteger(slKM) || slKM < 0) {
            event.preventDefault();
            alert('Số lượng khuyến mãi phải là số nguyên từ 0 trở lên.');
            return;
        }
        if (gia <= 0) {
            event.preventDefault();
            alert('Giá nhập phải lớn hơn 0.');
            return;
        }
        if (product && soLuong > Number(product.ConLai || 0)) {
            event.preventDefault();
            alert('Số lượng nhập không được vượt số lượng còn lại. Mặt hàng: ' + row.MaHang);
            return;
        }
    }
}
document.getElementById('pnOrder')?.addEventListener('change', loadOrder);
document.getElementById('pnAdd')?.addEventListener('click', addRow);
document.getElementById('frmPhieuNhap')?.addEventListener('submit', validateForm);
loadOrder();
</script>
