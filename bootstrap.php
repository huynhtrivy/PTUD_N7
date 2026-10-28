<?php
// Session files must stay outside the directory served by the web server.
$sessionDirectory = getenv('PTUD_SESSION_PATH');
$configuredDirectory = session_save_path();
if (!$sessionDirectory && ini_get('session.save_handler') === 'files'
    && (!$configuredDirectory || !is_writable($configuredDirectory))) {
    $sessionDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.ptud-n7-sessions';
}
if ($sessionDirectory) {
    if (!is_dir($sessionDirectory) && !mkdir($sessionDirectory, 0700, true) && !is_dir($sessionDirectory)) {
        throw new RuntimeException('Không tạo được thư mục lưu phiên. Vui lòng cấu hình PTUD_SESSION_PATH ngoài thư mục website.');
    }
    if (!is_writable($sessionDirectory)) {
        throw new RuntimeException('Thư mục lưu phiên không có quyền ghi.');
    }
    session_save_path($sessionDirectory);
}
if (!session_start()) {
    throw new RuntimeException('Không thể khởi tạo phiên đăng nhập.');
}
