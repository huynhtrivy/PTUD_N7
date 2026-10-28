# UC06 — Lập đơn đặt nhập hàng (Nguyễn Thị Hồng Thắm)

Chủ NPP lập đơn chờ nhận; Thủ kho lập phiếu nhập thực nhận để tăng tồn.
Không ghi nhận khuyến mãi riêng hoặc thanh toán thực tế ở bước đặt hàng.

Chạy kiểm tra đầu vào: `php tests/uc06_validation.php`.

Kiểm thử tích hợp trên CSDL thử nghiệm đã import `qlbh7.sql`:

1. Đăng nhập Chủ NPP, đặt 10 hàng giá 100.000, CK 5%, thuế 10%:
   tiền hàng 1.000.000, CK 50.000, thuế 95.000, tổng 1.045.000.
2. Lưu: có một DonNhapHang và chi tiết, trạng thái Chờ nhận hàng;
   TonKho không đổi. Thủ kho nhận 10 qua phiếu nhập thì tồn mới tăng 10.
3. Gửi lại cùng POST/RequestKey (kể cả sau timeout): nhận lại cùng mã đơn,
   không thêm đơn hoặc chi tiết; kiểm tra cả hai yêu cầu gửi đồng thời.
4. Mở biểu mẫu mới: RequestKey khác, được lập đơn mới.
5. Dữ liệu số sai, NCC/kho/hàng ngừng hoạt động, hàng trùng, SL/giá <= 0,
   thuế/CK ngoài 0–100: không lưu. Lỗi ghi chi tiết phải rollback cả đơn.
6. Khóa tài khoản hoặc đổi quyền sau khi mở form: POST không được lưu.
7. Hủy và xác nhận: biểu mẫu tải lại, không thêm đơn và không đổi tồn.

RequestKey dùng unique index có sẵn trong qlbh7.sql; không cần đổi schema.
